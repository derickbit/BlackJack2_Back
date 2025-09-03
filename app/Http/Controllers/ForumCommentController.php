<?php

namespace App\Http\Controllers;

use App\Models\ForumComment;
use App\Models\ForumTopic;
use App\Models\ForumMention;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ForumCommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = ForumComment::with(['user', 'topic', 'parent'])
                ->withCount(['likes', 'replies']);

            if ($request->has('topic_id')) {
                $query->where('forum_topic_id', $request->topic_id);
            }

            if ($request->has('parent_id')) {
                if ($request->parent_id === 'null' || $request->parent_id === null) {
                    $query->whereNull('parent_id'); // Apenas comentários principais
                } else {
                    $query->where('parent_id', $request->parent_id);
                }
            }
            // Removido o else que forçava whereNull('parent_id')
            // Agora retorna TODOS os comentários se não especificar parent_id

            $comments = $query->orderBy('created_at', 'asc')
                ->paginate($request->per_page ?? 20);

            // Adicionar informação se o usuário atual curtiu cada comentário
            $comments->getCollection()->each(function ($comment) {
                $comment->is_liked_by_user = $comment->isLikedByUser(Auth::id());
            });

            return response()->json($comments);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar comentários: ' . $e->getMessage(), [
                'request' => $request->all(),
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Erro interno do servidor',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'forum_topic_id' => 'required|exists:forum_topics,id',
            'parent_id' => 'nullable|exists:forum_comments,id',
            'conteudo' => 'required|string',
            'imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Verificar se o tópico está fechado
        $topic = ForumTopic::find($request->forum_topic_id);
        if ($topic->fechado && !Auth::user()->isAdmin()) {
            return response()->json(['message' => 'Tópico fechado para novos comentários'], 403);
        }

        $data = [
            'forum_topic_id' => $request->forum_topic_id,
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id,
            'conteudo' => $request->conteudo,
        ];

        // Upload da imagem se fornecida
        if ($request->hasFile('imagem')) {
            $image = $request->file('imagem');
            $imagePath = $image->store('forum/comments', 's3');
            $data['imagem'] = $imagePath;
        }

        $comment = ForumComment::create($data);

        // Processar menções no conteúdo
        ForumMention::createFromContent($comment->conteudo, $comment, Auth::id());

        return response()->json([
            'message' => 'Comentário criado com sucesso!',
            'comment' => $comment->load(['user', 'likes', 'replies'])
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ForumComment $comment)
    {
        $comment->load([
            'user',
            'topic',
            'parent.user',
            'replies.user',
            'replies.likes',
            'likes.user'
        ]);

        // Adicionar informação se o usuário atual curtiu o comentário
        $comment->is_liked_by_user = $comment->isLikedByUser(Auth::id());

        // Adicionar informação de likes para respostas
        $comment->replies->each(function ($reply) {
            $reply->is_liked_by_user = $reply->isLikedByUser(Auth::id());
        });

        return response()->json($comment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ForumComment $comment)
    {
        // Verificar se o usuário pode editar (autor ou admin)
        if (Auth::id() !== $comment->user_id && !Auth::user()->isAdmin()) {
            return response()->json(['message' => 'Não autorizado'], 403);
        }

        $request->validate([
            'conteudo' => 'sometimes|required|string',
            'imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['conteudo']);

        // Upload de nova imagem
        if ($request->hasFile('imagem')) {
            // Deletar imagem anterior se existir
            if ($comment->imagem) {
                Storage::disk('s3')->delete($comment->imagem);
            }

            $image = $request->file('imagem');
            $imagePath = $image->store('forum/comments', 's3');
            $data['imagem'] = $imagePath;
        }

        $comment->update($data);

        // Marcar como editado se não foi um admin que editou
        if (!Auth::user()->isAdmin()) {
            $comment->markAsEdited();
        }

        // Reprocessar menções se o conteúdo foi alterado
        if (isset($data['conteudo'])) {
            ForumMention::createFromContent($comment->conteudo, $comment, Auth::id());
        }

        return response()->json([
            'message' => 'Comentário atualizado com sucesso!',
            'comment' => $comment->fresh()->load(['user', 'likes', 'replies'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ForumComment $comment)
    {
        // Verificar se o usuário pode deletar (autor ou admin)
        if (Auth::id() !== $comment->user_id && !Auth::user()->isAdmin()) {
            return response()->json(['message' => 'Não autorizado'], 403);
        }

        // Deletar imagem se existir
        if ($comment->imagem) {
            Storage::disk('s3')->delete($comment->imagem);
        }

        $comment->delete();

        return response()->json(['message' => 'Comentário deletado com sucesso!']);
    }

    /**
     * Toggle like em um comentário
     */
    public function toggleLike(ForumComment $comment)
    {
        $liked = \App\Models\ForumLike::toggle(Auth::id(), $comment);

        return response()->json([
            'liked' => $liked,
            'total_likes' => $comment->fresh()->total_likes
        ]);
    }

    /**
     * Obter respostas de um comentário
     */
    public function replies(ForumComment $comment)
    {
        $replies = $comment->replies()
            ->with(['user', 'likes'])
            ->withCount('likes')
            ->orderBy('created_at', 'asc')
            ->get();

        // Adicionar informação de likes para cada resposta
        $replies->each(function ($reply) {
            $reply->is_liked_by_user = $reply->isLikedByUser(Auth::id());
        });

        return response()->json($replies);
    }

    /**
     * Buscar comentários hierárquicos de um tópico
     */
    public function getTopicComments(Request $request, $topicId)
    {
        try {
            // Buscar todos os comentários do tópico (principais e respostas)
            $allComments = ForumComment::with(['user', 'parent.user'])
                ->withCount(['likes', 'replies'])
                ->where('forum_topic_id', $topicId)
                ->orderBy('created_at', 'asc')
                ->get();

            // Adicionar informação de likes para cada comentário
            $allComments->each(function ($comment) {
                $comment->is_liked_by_user = $comment->isLikedByUser(Auth::id());
            });

            // Organizar hierarquicamente
            $mainComments = $allComments->whereNull('parent_id')->values();

            // Para cada comentário principal, adicionar suas respostas
            $mainComments->each(function ($comment) use ($allComments) {
                $comment->replies = $allComments->where('parent_id', $comment->id)->values();
            });

            return response()->json([
                'data' => $mainComments,
                'total' => $allComments->count(),
                'main_comments' => $mainComments->count(),
                'replies' => $allComments->whereNotNull('parent_id')->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar comentários hierárquicos: ' . $e->getMessage(), [
                'topic_id' => $topicId,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Erro interno do servidor',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

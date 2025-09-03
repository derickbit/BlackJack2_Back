<?php

namespace App\Http\Controllers;

use App\Models\ForumTopic;
use App\Models\ForumComment;
use App\Models\ForumLike;
use App\Models\ForumMention;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ForumTopicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();

            // Parâmetros de filtro
            $search = $request->get('search', '');
            $fixados = $request->boolean('fixados');
            $fechados = $request->boolean('fechados');
            $orderBy = $request->get('orderBy', 'recent');

            // Query base
            $query = ForumTopic::with(['user']);

            // Aplicar filtros
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('titulo', 'like', "%{$search}%")
                      ->orWhere('conteudo', 'like', "%{$search}%");
                });
            }

            if ($fixados) {
                $query->fixados();
            }

            if ($fechados) {
                $query->fechados();
            } else {
                $query->ativos();
            }

            // Ordenação
            switch ($orderBy) {
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'most_comments':
                    $query->withCount('allComments')
                          ->orderBy('all_comments_count', 'desc');
                    break;
                case 'most_likes':
                    $query->withCount('likes')
                          ->orderBy('likes_count', 'desc');
                    break;
                default: // 'recent'
                    $query->orderBy('fixado', 'desc')
                          ->orderBy('created_at', 'desc');
                    break;
            }

            // Paginação
            $topics = $query->paginate(15);

            // Adicionar informações de like do usuário
            if ($user) {
                $topics->each(function ($topic) use ($user) {
                    $topic->is_liked_by_user = $topic->isLikedByUser($user->id);
                });
            } else {
                $topics->each(function ($topic) {
                    $topic->is_liked_by_user = false;
                });
            }

            return response()->json([
                'data' => $topics->items(),
                'current_page' => $topics->currentPage(),
                'last_page' => $topics->lastPage(),
                'per_page' => $topics->perPage(),
                'total' => $topics->total(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao buscar tópicos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            $request->validate([
                'titulo' => 'required|string|max:255',
                'conteudo' => 'required|string',
                'imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $data = [
                'user_id' => $user->id,
                'titulo' => $request->titulo,
                'conteudo' => $request->conteudo,
                'visualizacoes' => 0,
            ];

            // Upload da imagem se fornecida
            if ($request->hasFile('imagem')) {
                $image = $request->file('imagem');

                // Determinar o disco baseado no ambiente
                $disk = app()->environment('production') ? 's3' : 'public';

                if ($disk === 's3') {
                    // Em produção, salva no S3 com visibilidade pública
                    $imagePath = $image->store('forum/topics', $disk, ['visibility' => 'public']);
                } else {
                    // Localmente, salva na pasta public
                    $imagePath = $image->store('forum/topics', $disk);
                }

                $data['imagem'] = $imagePath;
            }

            $topic = ForumTopic::create($data);

            // Processar menções
            $this->processMentions($topic, $request->conteudo);

            // Carregar relacionamentos para retorno
            $topic->load('user');
            $topic->is_liked_by_user = false;

            return response()->json($topic, 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Dados de validação inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao criar tópico',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $user = Auth::user();

            $topic = ForumTopic::with(['user'])->findOrFail($id);

            // Incrementar visualizações (apenas se não for o autor)
            if (!$user || $user->id !== $topic->user_id) {
                $topic->incrementViews();
            }

            // Adicionar informação de like do usuário
            if ($user) {
                $topic->is_liked_by_user = $topic->isLikedByUser($user->id);
            } else {
                $topic->is_liked_by_user = false;
            }

            return response()->json($topic);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Tópico não encontrado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao buscar tópico',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $user = Auth::user();
            $topic = ForumTopic::findOrFail($id);

            // Verificar permissões
            if ($user->id !== $topic->user_id && $user->role !== 'admin') {
                return response()->json([
                    'message' => 'Você não tem permissão para editar este tópico'
                ], 403);
            }

            $request->validate([
                'titulo' => 'required|string|max:255',
                'conteudo' => 'required|string',
                'imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $data = [
                'titulo' => $request->titulo,
                'conteudo' => $request->conteudo,
            ];

            // Upload da nova imagem se fornecida
            if ($request->hasFile('imagem')) {
                // Remover imagem antiga
                if ($topic->imagem) {
                    Storage::disk('s3')->delete($topic->imagem);
                }

                $image = $request->file('imagem');
                $imagePath = $image->store('forum/topics', 's3');
                $data['imagem'] = $imagePath;
            }

            $topic->update($data);

            // Processar menções
            $this->processMentions($topic, $request->conteudo);

            // Carregar relacionamentos para retorno
            $topic->load('user');
            $topic->is_liked_by_user = $topic->isLikedByUser($user->id);

            return response()->json($topic);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Tópico não encontrado'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Dados de validação inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar tópico',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = Auth::user();
            $topic = ForumTopic::findOrFail($id);

            // Verificar permissões
            if ($user->id !== $topic->user_id && $user->role !== 'admin') {
                return response()->json([
                    'message' => 'Você não tem permissão para excluir este tópico'
                ], 403);
            }

            // Remover imagem se existir
            if ($topic->imagem) {
                Storage::disk('s3')->delete($topic->imagem);
            }

            // Excluir tópico (cascade irá excluir comentários, likes e menções)
            $topic->delete();

            return response()->json([
                'message' => 'Tópico excluído com sucesso'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Tópico não encontrado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao excluir tópico',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle like em um tópico
     */
    public function toggleLike(string $id)
    {
        try {
            $user = Auth::user();
            $topic = ForumTopic::findOrFail($id);

            $existingLike = ForumLike::where([
                'user_id' => $user->id,
                'likeable_type' => ForumTopic::class,
                'likeable_id' => $topic->id,
            ])->first();

            if ($existingLike) {
                $existingLike->delete();
                $liked = false;
            } else {
                ForumLike::create([
                    'user_id' => $user->id,
                    'likeable_type' => ForumTopic::class,
                    'likeable_id' => $topic->id,
                ]);
                $liked = true;
            }

            return response()->json([
                'liked' => $liked,
                'total_likes' => $topic->fresh()->total_likes,
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Tópico não encontrado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao processar like',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Processar menções no conteúdo
     */
    private function processMentions(ForumTopic $topic, string $content)
    {
        // Regex para encontrar menções @username
        preg_match_all('/@(\w+)/', $content, $matches);

        if (!empty($matches[1])) {
            $usernames = array_unique($matches[1]);

            // Buscar usuários mencionados
            $mentionedUsers = \App\Models\User::whereIn('name', $usernames)->get();

            // Remover menções antigas deste tópico
            ForumMention::where([
                'mentionable_type' => ForumTopic::class,
                'mentionable_id' => $topic->id,
            ])->delete();

            // Criar novas menções
            foreach ($mentionedUsers as $mentionedUser) {
                // Não mencionar o próprio autor
                if ($mentionedUser->id !== $topic->user_id) {
                    ForumMention::create([
                        'user_id' => $mentionedUser->id,
                        'mentioned_by' => $topic->user_id,
                        'mentionable_type' => ForumTopic::class,
                        'mentionable_id' => $topic->id,
                        'read' => false,
                    ]);
                }
            }
        }
    }
}

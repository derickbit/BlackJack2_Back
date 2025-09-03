<?php

namespace App\Http\Controllers;

use App\Models\ForumLike;
use App\Models\ForumTopic;
use App\Models\ForumComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumLikeController extends Controller
{
    /**
     * Toggle like em um tópico ou comentário
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'likeable_type' => 'required|in:topic,comment',
            'likeable_id' => 'required|integer',
        ]);

        $likeableType = $request->likeable_type === 'topic' ? ForumTopic::class : ForumComment::class;
        $likeable = $likeableType::findOrFail($request->likeable_id);

        $liked = ForumLike::toggle(Auth::id(), $likeable);

        return response()->json([
            'liked' => $liked,
            'total_likes' => $likeable->fresh()->total_likes,
            'message' => $liked ? 'Like adicionado!' : 'Like removido!'
        ]);
    }

    /**
     * Obter usuários que curtiram um item
     */
    public function likedBy(Request $request)
    {
        $request->validate([
            'likeable_type' => 'required|in:topic,comment',
            'likeable_id' => 'required|integer',
        ]);

        $likeableType = $request->likeable_type === 'topic' ? ForumTopic::class : ForumComment::class;
        $likeable = $likeableType::findOrFail($request->likeable_id);

        $likes = $likeable->likes()
            ->with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 20);

        return response()->json($likes);
    }
}

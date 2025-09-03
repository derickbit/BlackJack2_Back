<?php

namespace App\Http\Controllers;

use App\Models\ForumMention;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumMentionController extends Controller
{
    /**
     * Obter menções do usuário autenticado
     */
    public function index(Request $request)
    {
        $query = ForumMention::with(['mentionedBy:id,name', 'mentionable'])
            ->forUser(Auth::id());

        if ($request->has('unread') && $request->boolean('unread')) {
            $query->naoLidas();
        }

        $mentions = $query->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 20);

        return response()->json($mentions);
    }

    /**
     * Marcar menção como lida
     */
    public function markAsRead(ForumMention $mention)
    {
        // Verificar se a menção pertence ao usuário autenticado
        if ($mention->user_id !== Auth::id()) {
            return response()->json(['message' => 'Não autorizado'], 403);
        }

        $mention->markAsRead();

        return response()->json(['message' => 'Menção marcada como lida']);
    }

    /**
     * Marcar todas as menções como lidas
     */
    public function markAllAsRead()
    {
        ForumMention::forUser(Auth::id())
            ->naoLidas()
            ->update(['lida' => true]);

        return response()->json(['message' => 'Todas as menções foram marcadas como lidas']);
    }

    /**
     * Contar menções não lidas
     */
    public function unreadCount()
    {
        $count = ForumMention::forUser(Auth::id())
            ->naoLidas()
            ->count();

        return response()->json(['count' => $count]);
    }
}

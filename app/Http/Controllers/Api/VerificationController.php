<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User; // Certifique-se que o caminho para User está correto
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\URL; // Para verificar a assinatura
use Illuminate\Http\JsonResponse;   // Para respostas JSON
use Illuminate\Support\Facades\Log; // Para logs

class VerificationController extends Controller
{
    /**
     * Marcar o e-mail do usuário como verificado.
     * (Chamado pelo link no e-mail)
     */
    public function verify(Request $request, $id, $hash)
    {
        $user = User::find($id);
        $frontendUrl = rtrim(env('FRONTEND_URL', 'http://localhost:3000'), '/'); // Pega a URL do frontend do .env

        if (!$user) {
            Log::warning('Verificação de email: Usuário não encontrado.', ['id' => $id]);
            return redirect($frontendUrl . '/falha-verificacao?erro=usuario_nao_encontrado');
        }

        // A middleware 'signed' já verifica a assinatura da URL inteira.
        // A verificação adicional do hash é uma camada extra de segurança específica para email.
        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            Log::warning('Verificação de email: Hash inválido.', ['id' => $id, 'hash_recebido' => $hash, 'hash_esperado' => sha1($user->getEmailForVerification())]);
            return redirect($frontendUrl . '/falha-verificacao?erro=hash_invalido');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect($frontendUrl . '/login?ja_verificado=true'); // Ou para a página de "email já verificado"
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect($frontendUrl . '/email-verificado'); // Redireciona para sua página de sucesso no frontend
    }

    /**
     * Reenviar o e-mail de verificação.
     * (Chamado pelo seu frontend)
     */
    public function resend(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Usuário não encontrado.'], 404);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Este e-mail já foi verificado.'], 400);
        }

        try {
            $user->sendEmailVerificationNotification();
            return response()->json(['message' => 'Link de verificação reenviado com sucesso!']);
        } catch (\Exception $e) {
            Log::error('Erro ao reenviar email de verificação: ' . $e->getMessage());
            return response()->json(['message' => 'Erro ao reenviar o link de verificação. Tente novamente mais tarde.'], 500);
        }
    }
}

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PartidaController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ReportMessageController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\VerificationController; // <<---- ADICIONE ESTE IMPORT
use App\Http\Controllers\AtualizacaoController;
use App\Http\Controllers\ForumTopicController;
use App\Http\Controllers\ForumCommentController;
use App\Http\Controllers\ForumLikeController;
use App\Http\Controllers\ForumMentionController;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Mail;

Route::middleware('api')->group(function () {

    // Rotas Públicas de API (não requerem autenticação)
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/partidas/ranking', [PartidaController::class, 'ranking'])->name('partidas.ranking.api');
    Route::get('/jogadores', [UserController::class, 'listarJogadores']);
    Route::apiResource('partidas', PartidaController::class)->only(['index', 'show'])->where(['partida' => '[0-9]+']); // Adicionei o where que sugeri antes
    Route::apiResource('users', UserController::class)->only(['index', 'show', 'store']); // store (registro) é público

    // Rotas públicas para atualizações (patch notes) - todos podem ler
    Route::get('/atualizacoes', [AtualizacaoController::class, 'index']);
    Route::get('/atualizacoes/{atualizacao}', [AtualizacaoController::class, 'show']);

    // Rotas públicas do fórum (visualização apenas)
    Route::get('/forum/topics', [ForumTopicController::class, 'index']);
    Route::get('/forum/topics/{topic}', [ForumTopicController::class, 'show']);

    // --- ROTAS DE VERIFICAÇÃO DE E-MAIL ---
    // Rota que o link no e-mail de verificação chamará.
    // O Laravel, por padrão, procura pelo nome 'verification.verify'.
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1']) // 'signed' é crucial para segurança
        ->name('verification.verify'); // <<---- NOME DEVE SER ESTE!


    // Rota para o frontend solicitar o reenvio do e-mail de verificação.
    // Corresponde ao que você tem no seu componente 'AguardeVerificacao'.
    Route::post('/email/resend-public', [VerificationController::class, 'resend'])
        ->middleware(['throttle:6,1']);

    Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);
    Password::sendResetLink($request->only('email'));
    return response()->json(['message' => 'Verifique seu email. Instruções para redefinir a senha foram enviadas pelo nosso email blackjacktcc@gmail.com.']);
});

Route::post('/reset-password', function (Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|confirmed',
    ]);
    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => bcrypt($password)
            ])->save();
        }
    );
    // Adicione este log para depurar
    \Log::info('Password reset status:', ['status' => $status]);
    return $status === Password::PASSWORD_RESET
        ? response()->json(['message' => 'Senha redefinida com sucesso!'])
        : response()->json(['message' => 'Erro ao redefinir senha.', 'status' => $status], 400);
});

    // --- Rotas Protegidas por Sanctum ---
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [LoginController::class, 'logout']);
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
        Route::post('/partidas/simular', [PartidaController::class, 'simularPartida']);
        Route::get('/partidas/user/jogo/{jogo}', [PartidaController::class, 'showByUserByGame']);
        Route::apiResource('partidas', PartidaController::class)->except(['index', 'show']);
        Route::apiResource('users', UserController::class)->except(['index', 'show', 'store']);
        Route::apiResource('reports', ReportController::class);
        Route::get('reports/{report}/messages', [ReportMessageController::class, 'index']);
        Route::post('reports/{report}/messages', [ReportMessageController::class, 'store']);
        Route::patch('/reports/{report}/status', [ReportController::class, 'updateStatus']);

        // Rotas protegidas para atualizações (patch notes) - apenas admins
        Route::post('/atualizacoes', [AtualizacaoController::class, 'store']);
        Route::put('/atualizacoes/{atualizacao}', [AtualizacaoController::class, 'update']);
        Route::patch('/atualizacoes/{atualizacao}', [AtualizacaoController::class, 'update']);
        Route::delete('/atualizacoes/{atualizacao}', [AtualizacaoController::class, 'destroy']);
        Route::patch('/atualizacoes/{atualizacao}/toggle-status', [AtualizacaoController::class, 'toggleStatus']);
        Route::delete('/atualizacoes/{atualizacao}/imagem', [AtualizacaoController::class, 'removeImagem']);

        // Rotas do Fórum
        Route::apiResource('forum/topics', ForumTopicController::class);
        Route::post('forum/topics/{topic}/like', [ForumTopicController::class, 'toggleLike']);

        Route::apiResource('forum/comments', ForumCommentController::class);
        Route::post('forum/comments/{comment}/like', [ForumCommentController::class, 'toggleLike']);
        Route::get('forum/comments/{comment}/replies', [ForumCommentController::class, 'replies']);
        Route::get('forum/topics/{topic}/comments', [ForumCommentController::class, 'getTopicComments']);

        Route::post('forum/like/toggle', [ForumLikeController::class, 'toggle']);
        Route::get('forum/like/liked-by', [ForumLikeController::class, 'likedBy']);

        Route::get('forum/mentions', [ForumMentionController::class, 'index']);
        Route::patch('forum/mentions/{mention}/read', [ForumMentionController::class, 'markAsRead']);
        Route::patch('forum/mentions/read-all', [ForumMentionController::class, 'markAllAsRead']);
        Route::get('forum/mentions/unread-count', [ForumMentionController::class, 'unreadCount']);
    });
});

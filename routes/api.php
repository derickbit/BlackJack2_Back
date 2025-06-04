<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PartidaController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ReportMessageController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\VerificationController; // <<---- ADICIONE ESTE IMPORT

Route::middleware('api')->group(function () {

    // Rotas Públicas de API (não requerem autenticação)
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/partidas/ranking', [PartidaController::class, 'ranking'])->name('partidas.ranking.api');
    Route::get('/jogadores', [UserController::class, 'listarJogadores']);
    Route::apiResource('partidas', PartidaController::class)->only(['index', 'show'])->where(['partida' => '[0-9]+']); // Adicionei o where que sugeri antes
    Route::apiResource('users', UserController::class)->only(['index', 'show', 'store']); // store (registro) é público

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


    // --- Rotas Protegidas por Sanctum ---
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [LoginController::class, 'logout']);
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
        Route::post('/partidas/simular', [PartidaController::class, 'simularPartida']);
        Route::get('/partidas/user', [PartidaController::class, 'showByUser']);
        Route::apiResource('partidas', PartidaController::class)->except(['index', 'show']);
        Route::apiResource('users', UserController::class)->except(['index', 'show', 'store']);
        Route::apiResource('reports', ReportController::class);
        Route::get('reports/{report}/messages', [ReportMessageController::class, 'index']);
        Route::post('reports/{report}/messages', [ReportMessageController::class, 'store']);
        Route::patch('/reports/{report}/status', [ReportController::class, 'updateStatus']);
    });
});

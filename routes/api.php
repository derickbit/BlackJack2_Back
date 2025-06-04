<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PartidaController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ReportMessageController;
use App\Http\Controllers\Api\LoginController;

Route::middleware('api')->group(function () {

    // --- Rotas Públicas ---
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/partidas/ranking', [PartidaController::class, 'ranking'])->name('partidas.ranking.api');
    Route::get('/jogadores', [UserController::class, 'listarJogadores']); // Verifique se esta é a rota que você quer para "/api/usuarios" no frontend

    // --- Rotas Protegidas por Sanctum ---
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [LoginController::class, 'logout']);
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        // Rota específica para partidas do usuário ANTES do apiResource geral de partidas
        Route::get('/partidas/user', [PartidaController::class, 'showByUser']);
        Route::post('/partidas/simular', [PartidaController::class, 'simularPartida']);

        // Rotas de API para Partidas - Protegidas (store, update, destroy)
        // Isso não inclui 'show' ou 'index' que são públicos ou tratados de forma diferente
        Route::apiResource('partidas', PartidaController::class)->except(['index', 'show']);

        // Rotas de API para Usuários - Protegidas (update, destroy)
        Route::apiResource('users', UserController::class)->except(['index', 'show', 'store']);

        // Reports
        Route::apiResource('reports', ReportController::class);
        Route::get('reports/{report}/messages', [ReportMessageController::class, 'index']);
        Route::post('reports/{report}/messages', [ReportMessageController::class, 'store']);
        Route::patch('/reports/{report}/status', [ReportController::class, 'updateStatus']);
    });

    // --- Rotas Públicas de Recurso (definidas por último para evitar conflitos com rotas específicas) ---
    // Se '/partidas/user' é autenticada, e '/partidas/{id}' (show) é pública, a ordem acima deve funcionar.
    // O middleware 'auth:sanctum' no grupo acima já garante que '/partidas/user' só é acessada por usuários autenticados.
    Route::apiResource('partidas', PartidaController::class)->only(['index', 'show']);

    // Rotas de API para Usuários - Públicas (index, show, store para registro)
    Route::apiResource('users', UserController::class)->only(['index', 'show', 'store']); // A rota para listar usuários é GET /api/users
});

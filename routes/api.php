<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PartidaController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ReportMessageController;
use App\Http\Controllers\Api\LoginController;
// Removi imports de Modelos, Password, Mail, etc., pois geralmente não são usados diretamente em arquivos de rota,
// a menos que você tenha closures de rota muito complexas (o que é desencorajado).
// Se você os usa em closures aqui, pode adicioná-los de volta.

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aqui é onde você pode registrar rotas de API para sua aplicação.
| Estas rotas são carregadas pelo RouteServiceProvider (ou no Laravel 11,
| configurado em bootstrap/app.php) dentro de um grupo que recebe
| o prefixo 'api' e o grupo de middleware 'api'.
|
*/

// Todas as rotas definidas neste arquivo agora usarão o grupo de middleware 'api'
// (definido em bootstrap/app.php) e já terão o prefixo '/api'
// (também definido em bootstrap/app.php).
Route::middleware('api')->group(function () {

    // Rotas Públicas de API (não requerem autenticação)
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/partidas/ranking', [PartidaController::class, 'ranking'])->name('partidas.ranking.api'); // Adicionei .api para evitar conflito de nome se tiver uma rota web com mesmo nome
    Route::get('/jogadores', [UserController::class, 'listarJogadores']);

    // Rotas de API para Partidas - Públicas (index e show)
    Route::apiResource('partidas', PartidaController::class)->only(['index', 'show']);

    // Rotas de API para Usuários - Públicas (index, show, store para registro)
    // Se 'store' é para registro de novos usuários, geralmente é público.
    Route::apiResource('users', UserController::class)->only(['index', 'show', 'store']);


    // --- Rotas Protegidas por Sanctum ---
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [LoginController::class, 'logout']);

        Route::get('/user', function (Request $request) {
            return $request->user();
        });
        // O middleware 'verified' geralmente não é usado com 'auth:sanctum' para APIs baseadas em token.
        // A verificação de email é um conceito mais ligado a sessões web.

        Route::post('/partidas/simular', [PartidaController::class, 'simularPartida']);
        Route::get('/partidas/user', [PartidaController::class, 'showByUser']);

        // Rotas de API para Partidas - Protegidas (store, update, destroy)
        Route::apiResource('partidas', PartidaController::class)->except(['index', 'show']);

        // Rotas de API para Usuários - Protegidas (update, destroy)
        Route::apiResource('users', UserController::class)->except(['index', 'show', 'store']);

        // Reports
        Route::apiResource('reports', ReportController::class); // Todas as rotas de ReportController são protegidas
        Route::get('reports/{report}/messages', [ReportMessageController::class, 'index']);
        Route::post('reports/{report}/messages', [ReportMessageController::class, 'store']);
        Route::patch('/reports/{report}/status', [ReportController::class, 'updateStatus']);
        // O middleware 'verified' foi removido daqui também.
    });


    // Rotas de teste (mantenha para depuração por enquanto)
    Route::post('/pingtestlama', function () {
        return response()->json(['message' => 'pong da API pingtestlama']);
    });
    Route::get('/getpingtest', function () {
        return response()->json(['message' => 'GET pong da API getpingtest']);
    });

});


// --- Rotas Relacionadas a Email e Senha ---
// Estas rotas geralmente são WEB e pertencem a routes/web.php, pois envolvem
// interação do usuário com o navegador, emails e redirects.
// Se você tem um fluxo de API muito específico para elas, pode mantê-las aqui,
// mas elas não estariam sob o grupo 'auth:sanctum' normalmente.
// Por agora, vou deixá-las fora do grupo Route::middleware('api')->group(...)
// para ver se o problema principal é resolvido.
// Se precisar delas como API, pense cuidadosamente sobre a autenticação e o fluxo.

// Route::get('/email/verify/{id}/{hash}', function (Request $request) { ... })->name('verification.verify.api');
// Route::post('/email/resend-public', function (Request $request) { ... });
// Route::post('/forgot-password', function (Request $request) { ... });
// Route::post('/reset-password', function (Request $request) { ... });

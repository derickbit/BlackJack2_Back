<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
// Se o seu App\Http\Middleware\TrustProxies usa Illuminate\Http\Request,
// o import dele já está lá dentro do arquivo TrustProxies.php.

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // --- MIDDLEWARES GLOBAIS ---
        // Aplique seu TrustProxies personalizado aqui, bem no início.
        $middleware->use([
            \App\Http\Middleware\TrustProxies::class, // <--- MOVIDO PARA CÁ (GLOBAL)
            // Se tiver outros middlewares globais, eles vêm depois ou antes,
            // mas TrustProxies geralmente é bom estar no início.
            // Exemplo: \Illuminate\Http\Middleware\HandleCors::class, (se o seu CORS for global)
        ]);

        // Definição do grupo de middleware 'api'
        $middleware->group('api', [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        // Definição do grupo de middleware 'web'
        $middleware->group('web', [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            // \App\Http\Middleware\TrustProxies::class, // <--- REMOVA DE DENTRO DO GRUPO 'web' SE APLICOU GLOBALMENTE
        ]);

        // Aliases de middleware
        // $middleware->alias([
        //     'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // ...
    })->create();

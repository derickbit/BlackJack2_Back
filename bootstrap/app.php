<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // 1. Configura o TrustProxies globalmente para o Heroku
        $middleware->trustProxies(
            '*', // proxies
            Request::HEADER_X_FORWARDED_FOR |
            Request::HEADER_X_FORWARDED_HOST |
            Request::HEADER_X_FORWARDED_PORT |
            Request::HEADER_X_FORWARDED_PROTO |
            Request::HEADER_X_FORWARDED_AWS_ELB // headers
        );

        // 2. <<< MUDANÇA PRINCIPAL: Configura o CORS nativo do Laravel >>>
        $middleware->handleCors(
            paths: ['api/*'], // Aplica a todas as rotas de API
            allowedOrigins: [env('FRONTEND_URL', 'http://localhost:3000')], // Usa sua variável de ambiente
            allowedMethods: ['*'], // Permite todos os métodos (GET, POST, PUT, etc.)
            allowedHeaders: ['*'], // Permite todos os cabeçalhos
            // exposedHeaders: ['Authorization'], // Se o frontend precisar ler algum header específico da resposta
            // maxAge: 3600,
        );


        // Define os grupos de middleware
        $middleware->group('api', [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        $middleware->group('web', [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        // Define os aliases de middleware
        $middleware->alias([
            'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php', // Garante que seu arquivo routes/api.php seja carregado
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api', // Define o prefixo /api para todas as rotas em routes/api.php
        // A linha apiMiddleware foi removida daqui, pois não é um parâmetro válido aqui.
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Definição do grupo de middleware 'api'
        // Este grupo será aplicado às suas rotas de API através do routes/api.php
        $middleware->group('api', [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class, // Descomente se estiver usando Sanctum para autenticação de SPA baseada em cookies
            'throttle:api', // Rate limiting padrão para APIs (ex: 60 requisições por minuto)
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            // Se você tiver um middleware de CORS configurado e quiser aplicá-lo apenas à API:
            // \App\Http\Middleware\HandleCors::class, // Ou o caminho para o seu middleware de CORS
        ]);

        // Definição do grupo de middleware 'web'
        // Importante para suas rotas web (sessões, CSRF, etc.)
        $middleware->group('web', [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            // Se você criou App\Http\Middleware\TrustProxies::class, adicione aqui se não for global
             \App\Http\Middleware\TrustProxies::class, // Para Heroku, geralmente necessário no grupo web
        ]);

        // Middlewares Globais (executados em todas as requisições HTTP)
        // Se TrustProxies não estiver nos grupos acima e você precisar dele globalmente:
        // $middleware->use([
        //     \App\Http\Middleware\TrustProxies::class,
        // ]);

        // Se você usa o HandleCors do Laravel e quer que seja global:
        // $middleware->prepend(\Illuminate\Http\Middleware\HandleCors::class);

        // Aliases de middleware (se você os usa nas suas rotas)
        // Exemplo:
        // $middleware->alias([
        //     'auth' => \App\Http\Middleware\Authenticate::class,
        //     'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class, // Para verificação de email em rotas web
        //     'auth.sanctum' => \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class, // Alias comum para Sanctum
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Configuração de tratamento de exceções aqui, se necessário
    })->create();

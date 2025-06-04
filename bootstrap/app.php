<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request; // Necessário para as constantes de header do TrustProxies

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Configura TrustProxies globalmente usando o helper do Laravel 11
        // Isso usa o middleware Illuminate\Http\Middleware\TrustProxies interno,
        // mas o configura com suas especificações.
        $middleware->trustProxies(
            proxies: '*', // Ou a configuração do seu App\Http\Middleware\TrustProxies::$proxies
            headers: Request::HEADER_X_FORWARDED_FOR |
                     Request::HEADER_X_FORWARDED_HOST |
                     Request::HEADER_X_FORWARDED_PORT |
                     Request::HEADER_X_FORWARDED_PROTO |
                     Request::HEADER_X_FORWARDED_AWS_ELB // Ou a configuração do seu App\Http\Middleware\TrustProxies::$headers
        );

        // O middleware \Fruitcake\Cors\HandleCors::class (se você usa o pacote fruitcake/laravel-cors)
        // geralmente é adicionado globalmente pelo seu Service Provider.
        // Não precisa adicioná-lo explicitamente aqui a menos que saiba que não está funcionando.

        // Middlewares padrões do Laravel 11 como TrimStrings, ConvertEmptyStringsToNull, ValidatePostSize,
        // PreventRequestsDuringMaintenance geralmente são adicionados pela própria framework
        // ou através de outros helpers/configurações.

        // --- Seus grupos de middleware ---
        $middleware->group('api', [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api', // Garanta que o alias 'throttle:api' esteja definido ou use a classe completa. Padrão: 'throttle:60,1'
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        $middleware->group('web', [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class, // Adicione se usar autenticação de sessão padrão
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        // --- Aliases (se necessário) ---
        $middleware->alias([
            'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
             // O alias 'throttle' é geralmente definido por padrão.
             // Se 'throttle:api' não funcionar, verifique se o alias 'throttle' está presente,
             // ou use a classe completa: \Illuminate\Routing\Middleware\ThrottleRequests::class.
             // Ex: 'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // ...
    })->create();

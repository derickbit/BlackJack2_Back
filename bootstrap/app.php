<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request; // Import necessário para as constantes de header do TrustProxies

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api', // Define o prefixo /api para todas as rotas em routes/api.php
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Configura TrustProxies globalmente usando o helper do Laravel 11
        // com ARGUMENTOS POSICIONAIS.
        // Isso configura a classe \Illuminate\Http\Middleware\TrustProxies interna do Laravel.
        $middleware->trustProxies(
            '*', // 1º argumento: $proxies (confia em todos os proxies - comum para Heroku)
            Request::HEADER_X_FORWARDED_FOR |
            Request::HEADER_X_FORWARDED_HOST |
            Request::HEADER_X_FORWARDED_PORT |
            Request::HEADER_X_FORWARDED_PROTO |
            Request::HEADER_X_FORWARDED_AWS_ELB // 2º argumento: $headers (confia nesses cabeçalhos)
        );

        // NOTA SOBRE MIDDLEWARES GLOBAIS:
        // 1. O \Fruitcake\Cors\HandleCors::class (do pacote fruitcake/laravel-cors)
        //    deve ser registrado globalmente pelo seu Service Provider. Normalmente,
        //    você não precisa adicioná-lo aqui explicitamente. Se o CORS falhar
        //    após esta correção, verifique sua variável de ambiente FRONTEND_URL
        //    e o arquivo config/cors.php.
        //
        // 2. Outros middlewares globais padrão do Laravel (TrimStrings, ConvertEmptyStringsToNull,
        //    ValidatePostSize, PreventRequestsDuringMaintenance) são tipicamente
        //    adicionados pela própria framework no Laravel 11. Evite adicioná-los
        //    manualmente com $middleware->use([]) a menos que seja estritamente necessário
        //    para uma customização específica de ordem ou comportamento.

        // Define os grupos de middleware
        $middleware->group('api', [
            // Se estiver usando Sanctum para autenticação de SPA baseada em cookies, descomente:
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,

            'throttle:api', // Rate limiting para o grupo 'api'. Ex: '60,1' (60 requisições por minuto)
                            // O alias 'throttle' precisa estar definido (veja abaixo).

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
            // Não é mais necessário adicionar \App\Http\Middleware\TrustProxies::class aqui,
            // pois foi configurado globalmente acima com o helper.
        ]);

        // Define os aliases de middleware
        $middleware->alias([
            'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class, // Garante que o alias 'throttle' esteja disponível
            // Adicione quaisquer outros aliases que sua aplicação utilize:
            // 'auth' => \App\Http\Middleware\Authenticate::class,
            // 'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Configure o tratamento de exceções aqui, se necessário.
        // Exemplo para retornar JSON em erros de API:
        // if ($exceptions->shouldRenderJsonWhen($request, $e)) {
        //     // Manipulação customizada de erro para JSON
        // }

        // Exemplo de não reportar uma exceção customizada:
        // $exceptions->dontReport([
        //     \App\Exceptions\MyCustomNonReportableException::class,
        // ]);
    })->create();

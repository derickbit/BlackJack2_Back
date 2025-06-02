<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware; // Corrigido para o namespace correto

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api', // Garanta que esta linha existe e está correta
        // Adicione esta linha para aplicar o grupo de middleware 'api' às suas rotas de API:
        apiMiddleware: ['api'],
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Middleware global (executado em todas as requisições)
        // Exemplo: se você precisar do TrustProxies globalmente (já deve estar configurado se você criou o arquivo e ele é autodescoberto ou registrado)
        // $middleware->use([
        // \App\Http\Middleware\TrustProxies::class, // Verifique se este é o caminho correto do seu TrustProxies
        // ]);

        // Middlewares de API (o grupo 'api')
        $middleware->group('api', [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class, // Se estiver a usar Sanctum para SPAs
            'throttle:api', // Middleware de rate limiting padrão para API
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            // Outros middlewares específicos para API podem ir aqui
        ]);

        // Middlewares da Web (o grupo 'web') - importante para rotas web
        $middleware->group('web', [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            // Outros middlewares para o grupo 'web'
        ]);

        // Aliases de middleware (se você precisar deles)
        // Exemplo:
        // $middleware->alias([
        // 'auth' => \App\Http\Middleware\Authenticate::class,
        // 'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class
        // ]);

        // Middleware de CORS (Exemplo, se você estiver usando o handler do Laravel)
        // $middleware->prepend(\Illuminate\Http\Middleware\HandleCors::class); // Para CORS global
        // Ou adicione ao grupo 'api' se for específico para API

        // Adicione o seu TrustProxies.php ao middleware global se ele não for carregado automaticamente
        // Normalmente, para o Heroku, você o teria configurado internamente (protected $proxies = '*')
        // E o Laravel o aplicaria. Se você tem app/Http/Middleware/TrustProxies.php, ele deve ser
        // incluído na lista de middleware global automaticamente ou você pode adicioná-lo aqui.
        // A forma como o Laravel 11 carrega middleware padrão mudou um pouco, alguns são por convenção.
        // Se `TrustProxies` não estiver a funcionar, pode ser necessário adicioná-lo explicitamente:
         $middleware->prependToGroup('web', \App\Http\Middleware\TrustProxies::class); // Ou adicione globalmente
         $middleware->prependToGroup('api', \App\Http\Middleware\TrustProxies::class); // Se necessário para API também

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

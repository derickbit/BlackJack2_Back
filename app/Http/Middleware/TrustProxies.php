<?php
namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request; // Garanta que esta linha 'use' esteja presente

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * Para o Heroku, é comum usar '*' para confiar em todos os proxies,
     * já que o Heroku lida com o tráfego através de seus próprios load balancers.
     *
     * @var array|string|null
     */
    protected $proxies = '*'; // Esta é a configuração mais simples para Heroku

    /**
     * The headers that should be used to detect proxies.
     *
     * A constante Request::HEADER_X_FORWARDED_ALL é uma forma abrangente.
     * Ou você pode ser mais específico como abaixo.
     *
     * @var int
     */
    protected $headers = Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_HOST | Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO | Request::HEADER_X_FORWARDED_AWS_ELB;

    // Alternativamente, para $headers, você pode usar:
    // protected $headers = Request::HEADER_X_FORWARDED_ALL;
    // Ambas as abordagens para $headers são válidas. A linha acima é mais concisa.
    // Se a linha com Request::HEADER_X_FORWARDED_AWS_ELB não funcionar como esperado,
    // tente Request::HEADER_X_FORWARDED_ALL.
}

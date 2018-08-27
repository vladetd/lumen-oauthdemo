<?php
/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 15.7.18
 * Time: 15:33
 */

namespace App\Http\Middleware;

use Closure;
use League\OAuth2\Server\Middleware\ResourceServerMiddleware;
use League\OAuth2\Server\ResourceServer;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;


class OauthMiddleware
{
    private $resourceServer;

    /**
     * OauthMiddleware constructor.
     * @param ResourceServer $resourceServer
     */
    public function __construct(ResourceServer $resourceServer)
    {
        $this->resourceServer = $resourceServer;
    }


    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @throws
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $psr7Factory = new DiactorosFactory();
        $psrRequest = $psr7Factory->createRequest($request);

        try {
            $resultRequest = $this->resourceServer->validateAuthenticatedRequest($psrRequest);
        } catch (\Exception $e) {
            var_dump($e); exit;
        }

        $httpFoundationFactory = new HttpFoundationFactory();
        $symfonyRequest = $httpFoundationFactory->createRequest($resultRequest);


        return $next($symfonyRequest);
    }
}
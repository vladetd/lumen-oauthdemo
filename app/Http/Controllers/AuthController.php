<?php
/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 15.7.18
 * Time: 20:48
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use League\OAuth2\Server\AuthorizationServer;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AuthController
 * @package App\Http\Controllers
 */
class AuthController extends Controller
{
    /**
     * @param Request $request
     * @throws \Illuminate\Validation\ValidationException
     * @throws \League\OAuth2\Server\Exception\OAuthServerException
     */
    public function issueAccessToken(Request $request)
    {
        /** @var AuthorizationServer $oauthService */
        $oauthService = app(AuthorizationServer::class);

        $psr7Factory = new DiactorosFactory();
        $psrRequest = $psr7Factory->createRequest($request);
        $psrResponse = $psr7Factory->createResponse(new Response('Content'));

        return $oauthService->respondToAccessTokenRequest($psrRequest, $psrResponse);
    }
}
<?php

namespace App\Providers;

use App\Services\Oauth\Repositories\AccessTokenRepository;
use App\Services\Oauth\Repositories\ClientRepository;
use App\Services\Oauth\Repositories\RefreshTokenRepository;
use App\Services\Oauth\Repositories\ScopeRepository;
use App\Services\Oauth\Repositories\UserRepository;
use Defuse\Crypto\Key;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use League\OAuth2\Server\ResourceServer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @throws
     * @return void
     */
    public function register()
    {
        $db = new DB();

        $authServer = new AuthorizationServer(
            new ClientRepository($db),
            new AccessTokenRepository($db),
            new ScopeRepository($db),
            file_get_contents(storage_path('private.key')),
            Key::loadFromAsciiSafeString("def000002000cc73e607027287ab92eddb32c88b399a582ce9306df53b210d09d58a1483afc818ef0b110fa01ab9c74f6607a73a1c846e1859bdf69aec4574ef242722b3")
        );

        $resourceServer = new ResourceServer(
            new AccessTokenRepository($db),
            file_get_contents(storage_path('public.key'))
        );

        $grant = new \League\OAuth2\Server\Grant\PasswordGrant(
            new UserRepository($db),
            new RefreshTokenRepository($db)
        );

        $grant->setRefreshTokenTTL(new \DateInterval('P1M')); // refresh tokens will expire after 1 month
        $authServer->enableGrantType(
            $grant,
            new \DateInterval('PT1H') // access tokens will expire after 1 hour
        );


        $refreshTokenGrant = new RefreshTokenGrant(
            new RefreshTokenRepository($db)
        );
        $authServer->enableGrantType(
            $refreshTokenGrant
        );

        $this->app->instance(AuthorizationServer::class, $authServer);
        $this->app->instance(ResourceServer::class, $resourceServer);
    }
}

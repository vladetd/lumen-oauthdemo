<?php
/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 15.7.18
 * Time: 15:18
 */

namespace App\Services\Oauth\Repositories;


use App\Services\Oauth\Entities\AccessTokenEntity;
use Illuminate\Support\Facades\DB;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;

class AccessTokenRepository implements AccessTokenRepositoryInterface
{
    /** @var DB */
    private $db;

    /** @var string  */
    private CONST table = 'oauth_access_tokens';

    /**
     * ClientRepository constructor.
     * @param DB $db
     */
    public function __construct(DB $db)
    {
        $this->db = $db;
    }

    /**
     * Create a new access token
     *
     * @param ClientEntityInterface $clientEntity
     * @param ScopeEntityInterface[] $scopes
     * @param mixed $userIdentifier
     *
     * @return AccessTokenEntityInterface
     */
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        $accessToken = new AccessTokenEntity();
        $accessToken->setClient($clientEntity);
        foreach ($scopes as $scope) {
            $accessToken->addScope($scope);
        }

        if (null !== $userIdentifier) {
            $accessToken->setUserIdentifier($userIdentifier);
        }

        return $accessToken;
    }

    /**
     * Persists a new access token to permanent storage.
     *
     * @param AccessTokenEntityInterface $accessTokenEntity
     *
     * @throws UniqueTokenIdentifierConstraintViolationException
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity)
    {
        $accessTokenId = DB::table(self::table)->insertGetId([
            'access_token' => $accessTokenEntity->getIdentifier(),
            'client_id' => $accessTokenEntity->getClient()->getIdentifier(),
            'expiry_timestamp' => $accessTokenEntity->getExpiryDateTime()->getTimestamp(),
            'member_id' => $accessTokenEntity->getUserIdentifier()
        ]);

        $scopes = [];
        foreach ($accessTokenEntity->getScopes() as $scope) {
            $this->db::table('oauth_access_tokens_scopes')->insert([
                'access_token_id' => $accessTokenId,
                'scope_id' => $scope->getIdentifier()
            ]);
        }
    }

    /**
     * Revoke an access token.
     *
     * @param string $tokenId
     */
    public function revokeAccessToken($tokenId)
    {
        $this->db::table(self::table)
            ->where('access_token', 'like', $tokenId)
            ->update([
                'expiry_timestamp' => time()
            ]);
    }

    /**
     * Check if the access token has been revoked.
     *
     * @param string $tokenId
     *
     * @return bool Return true if this token has been revoked
     */
    public function isAccessTokenRevoked($tokenId)
    {
        try {
            $accessTokens = $this->db::table(self::table)->where(
                'access_token', 'like', $tokenId
            )->get();
        } catch (\Exception $e) {
            var_dump($e); exit;
        }

        $accessToken = null;

        if ($accessTokens->isNotEmpty()) {
            $accessToken = $accessTokens->first();
        }

        return $accessToken->expiry_timestamp < time();
    }
}
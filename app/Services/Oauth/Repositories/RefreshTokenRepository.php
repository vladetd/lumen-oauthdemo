<?php
/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 15.7.18
 * Time: 15:18
 */

namespace App\Services\Oauth\Repositories;


use App\Services\Oauth\Entities\RefreshTokenEntity;
use Illuminate\Support\Facades\DB;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;

class RefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    /** @var DB */
    private $db;

    /** @var string  */
    private CONST table = 'oauth_refresh_tokens';

    /**
     * ClientRepository constructor.
     * @param DB $db
     */
    public function __construct(DB $db)
    {
        $this->db = $db;
    }

    /**
     * Creates a new refresh token
     *
     * @return RefreshTokenEntityInterface
     */
    public function getNewRefreshToken()
    {
        return new RefreshTokenEntity();
    }

    /**
     * Create a new refresh token_name.
     *
     * @param RefreshTokenEntityInterface $refreshTokenEntity
     *
     * @throws UniqueTokenIdentifierConstraintViolationException
     */
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity)
    {
        DB::table(self::table)->insert([
            'refresh_token' => $refreshTokenEntity->getIdentifier(),
            'access_token' => $refreshTokenEntity->getAccessToken()->getIdentifier(),
            'expiry_timestamp' => $refreshTokenEntity->getExpiryDateTime()->getTimestamp(),
        ]);
    }

    /**
     * Revoke the refresh token.
     *
     * @param string $tokenId
     */
    public function revokeRefreshToken($tokenId)
    {
        $this->db::table(self::table)
            ->where('refresh_token', 'like', $tokenId)
            ->update([
                'expiry_timestamp' => time()
            ]);
    }

    /**
     * Check if the refresh token has been revoked.
     *
     * @param string $tokenId
     *
     * @return bool Return true if this token has been revoked
     */
    public function isRefreshTokenRevoked($tokenId)
    {
        $refreshTokens = $this->db::table(self::table)->where(
            'refresh_token', 'like', $tokenId
        )->get(['expiry_timestamp']);

        $refreshToken = null;

        if ($refreshTokens->isNotEmpty()) {
            $refreshToken = $refreshTokens->first();
        }

        return $refreshToken->expiry_timestamp < time();
    }
}
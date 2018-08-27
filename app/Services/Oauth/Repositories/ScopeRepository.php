<?php
/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 15.7.18
 * Time: 15:19
 */

namespace App\Services\Oauth\Repositories;


use App\Services\Oauth\Entities\ScopeEntity;
use Illuminate\Support\Facades\DB;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;

class ScopeRepository implements ScopeRepositoryInterface
{
    /** @var DB */
    private $db;

    /** @var string  */
    private CONST table = 'oauth_scopes';

    /**
     * ClientRepository constructor.
     * @param DB $db
     */
    public function __construct(DB $db)
    {
        $this->db = $db;
    }

    /**
     * Return information about a scope.
     *
     * @param string $identifier The scope identifier
     *
     * @return ScopeEntityInterface
     */
    public function getScopeEntityByIdentifier($identifier)
    {
        $result = $this->db::table(self::table)
            ->get(['name'])
            ->where('name', 'like', $identifier);

        if ($result->isNotEmpty()) {
            return new ScopeEntity(
                $result->first()->name
            );
        }

        return;
    }

    /**
     * Given a client, grant type and optional user identifier validate the set of scopes requested are valid and optionally
     * append additional scopes or remove requested scopes.
     *
     * @param ScopeEntityInterface[] $scopes
     * @param string $grantType
     * @param ClientEntityInterface $clientEntity
     * @param null|string $userIdentifier
     *
     * @return ScopeEntityInterface[]
     */
    public function finalizeScopes(
        array $scopes,
        $grantType,
        ClientEntityInterface $clientEntity,
        $userIdentifier = null
    )
    {
        // Check if the scopes for that client are okay
        // These will probably be in the db
        $clientApp1 = 'app1';
        $scopesForApp1 = 'member';

        if ($clientEntity->getName() == $clientApp1 && $scopesForApp1 == 'member') {
            return $scopes;
        }
    }
}
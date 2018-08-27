<?php
/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 15.7.18
 * Time: 15:18
 */

namespace App\Services\Oauth\Repositories;


use App\Services\Oauth\Entities\ClientEntity;
use Illuminate\Support\Facades\DB;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

class ClientRepository implements ClientRepositoryInterface
{
    /** @var DB */
    private $db;

    /** @var string  */
    private CONST table = 'oauth_clients';

    /**
     * ClientRepository constructor.
     * @param DB $db
     */
    public function __construct(DB $db)
    {
        $this->db = $db;
    }


    /**
     * Get a client.
     *
     * @param string $clientIdentifier The client's identifier
     * @param null|string $grantType The grant type used (if sent)
     * @param null|string $clientSecret The client's secret (if sent)
     * @param bool $mustValidateSecret If true the client must attempt to validate the secret if the client
     *                                        is confidential
     *
     * @return ClientEntityInterface
     */
    public function getClientEntity($clientIdentifier, $grantType = null, $clientSecret = null, $mustValidateSecret = true)
    {
        $result = $this->db::table(self::table)
            ->get(['id', 'name'])
            ->where('name', 'like', $clientIdentifier);


        if ($result->isNotEmpty()) {
            return new ClientEntity(
                $result->first()->id,
                $result->first()->name
            );
        }

        return;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 15.7.18
 * Time: 15:19
 */

namespace App\Services\Oauth\Repositories;


use App\Services\Oauth\Entities\UserEntity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    /** @var DB */
    private $db;

    /** @var string  */
    private CONST table = 'members';

    /**
     * ClientRepository constructor.
     * @param DB $db
     */
    public function __construct(DB $db)
    {
        $this->db = $db;
    }

    /**
     * Get a user entity.
     *
     * @param string $username
     * @param string $password
     * @param string $grantType The grant type used
     * @param ClientEntityInterface $clientEntity
     *
     * @return UserEntityInterface
     */
    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    )
    {
        $hashedPassword = Hash::make($password);

        $result = $this->db::table(self::table)
            ->get(['id', 'username'])
            ->where('username', 'like', $username);


        if ($result->isNotEmpty()) {
            $user = $result->first();

            if (Hash::check($password, $hashedPassword)) {
                return new UserEntity(
                    $user->id
                );
            }
        }

        return;

    }
}
<?php
/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 16.7.18
 * Time: 19:14
 */

namespace App\Services\Oauth\Entities;


use League\OAuth2\Server\Entities\UserEntityInterface;

class UserEntity implements UserEntityInterface
{
    /**
     * @var string
     */
    private $identifier;

    /**
     * UserEntity constructor.
     * @param $identifier
     */
    public function __construct($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * Return the user's identifier.
     *
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }
}
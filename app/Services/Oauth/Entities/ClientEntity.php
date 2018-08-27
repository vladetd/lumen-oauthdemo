<?php
/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 16.7.18
 * Time: 18:41
 */

namespace App\Services\Oauth\Entities;


use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\ClientTrait;

class ClientEntity implements ClientEntityInterface
{
    use ClientTrait;

    /**
     * ClientEntity constructor.
     * @param $identifier
     * @param $name
     */
    public function __construct($identifier, $name)
    {
        $this->identifier = $identifier;
        $this->name = $name;
    }

    /**
     * @var int
     */
    private $identifier;

    /**
     * Get the client's identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }
}
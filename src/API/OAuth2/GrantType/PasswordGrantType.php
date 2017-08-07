<?php

namespace ITS\Trustpilot\API\OAuth2\GrantType;

use ITS\Trustpilot\API\OAuth2\GrantType;

class PasswordGrantType extends GrantType
{
    /** @var  string */
    protected $username;

    /** @var  string */
    protected $password;

    /**
     * PasswordGrantType constructor.
     * @param string $apiKey
     * @param string $apiSecret
     * @param string $username
     * @param string $password
     */
    public function __construct($apiKey, $apiSecret, $username, $password)
    {
        parent::__construct(self::TYPE_PASSWORD, $apiKey, $apiSecret);

        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @return array
     */
    public function getPayload()
    {
        return [
            'grant_type' => $this->grantType,
            'username'   => $this->username,
            'password'   => $this->password,
        ];
    }
}
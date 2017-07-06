<?php

namespace ITS\Trustpilot\API\OAuth2;

abstract class GrantType
{
    const TYPE_PASSWORD           = 'password';
    const TYPE_AUTHORIZATION_CODE = 'authorization_code';
    const TYPE_REFRESH_TOKEN      = 'refresh_token';

    /** @var  string */
    protected $grantType;

    /** @var  string */
    protected $apiKey;

    /** @var  string */
    protected $apiSecret;

    /**
     * PasswordGrantType constructor.
     * @param string $grantType
     * @param string $apiKey
     * @param string $apiSecret
     */
    public function __construct($grantType, $apiKey, $apiSecret)
    {
        $this->grantType = $grantType;
        $this->apiKey    = $apiKey;
        $this->apiSecret = $apiSecret;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->grantType;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @return string
     */
    public function getApiSecret()
    {
        return $this->apiSecret;
    }

    /**
     * @return array
     */
    public abstract function getPayload();
}
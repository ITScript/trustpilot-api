<?php

namespace ITS\Trustpilot\API\Endpoint;

class OAuth2 extends \ITS\Trustpilot\API\Endpoint
{
    /**
     * @var string
     */
    protected $apiBasePath = 'v1/';

    /**
     * Declares routes to be used by this resource.
     */
    protected function setUpRoutes()
    {
        parent::setUpRoutes();

        $this->setRoutes([
            'obtainAccessToken'  => 'oauth/oauth-business-users-for-applications/accesstoken',
            'refreshAccessToken' => 'oauth/oauth-business-users-for-applications/refresh',
            'revokeAccessToken'  => 'oauth/oauth-business-users-for-applications/revoke',
        ]);
    }

    /**
     * @param array $params
     *
     * @throws \Exception
     * @return \ITS\Trustpilot\API\OAuth2\AccessToken
     */
    public function obtainAccessToken(array $params = [])
    {
        $params = array_merge($this->client->getGrantType()->getPayload(), $params);
        $result = $this->client->post(
            $this->getRoute(__FUNCTION__),
            $params,
            [
                'auth' => [$this->client->getGrantType()->getApiKey(), $this->client->getGrantType()->getApiSecret()]
            ]
        );

        return $o = new \ITS\Trustpilot\API\OAuth2\AccessToken(
            $result->access_token,
            $result->refresh_token,
            new \DateTime('@' . (time() + $result->expires_in))
        );
    }

    /**
     * @param array $params
     *
     * @throws \Exception
     * @return \ITS\Trustpilot\API\OAuth2\AccessToken
     */
    public function refreshAccessToken(array $params = [])
    {
        $params = array_merge([
            'grant_type'    => \ITS\Trustpilot\API\OAuth2\GrantType::TYPE_REFRESH_TOKEN,
            'refresh_token' => $this->client->getAccessToken()->getRefreshToken()
        ], $params);

        $result = $this->client->post(
            $this->getRoute(__FUNCTION__),
            $params,
            [
                'auth' => [$this->client->getGrantType()->getApiKey(), $this->client->getGrantType()->getApiSecret()]
            ]
        );

        return $o = new \ITS\Trustpilot\API\OAuth2\AccessToken(
            $result->access_token,
            $result->refresh_token,
            new \DateTime('@' . (time() + $result->expires_in))
        );
    }

    /**
     * @param array $params
     *
     * @throws \Exception
     * @return null|\StdClass
     */
    public function revokeAccessToken(array $params = [])
    {
        $params = array_merge([
            'token' => $this->client->getAccessToken()->getRefreshToken()
        ], $params);

        return $this->client->post($this->getRoute(__FUNCTION__), $params);
    }
}
<?php

namespace ITS\Trustpilot\API\Endpoint;

class OAuth2 extends \ITS\Trustpilot\API\Endpoint
{
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
     * @link https://developers.trustpilot.com/authentication#password
     *
     * @param array $params
     *
     * @throws \Exception
     * @return \ITS\Trustpilot\API\OAuth2\AccessToken
     */
    public function obtainAccessToken(array $params = [])
    {
        $params = array_merge($this->getClient()->getGrantType()->getPayload(), $params);
        $result = $this->getClient()->post(
            $this->getRoute(__FUNCTION__),
            $params,
            [
                'auth' => [$this->getClient()->getGrantType()->getApiKey(), $this->getClient()->getGrantType()->getApiSecret()]
            ]
        );

        return $o = new \ITS\Trustpilot\API\OAuth2\AccessToken(
            $result->access_token,
            $result->refresh_token,
            new \DateTime('@' . (time() + $result->expires_in))
        );
    }

    /**
     * @link https://developers.trustpilot.com/authentication
     *
     * @param array $params
     *
     * @throws \Exception
     * @return \ITS\Trustpilot\API\OAuth2\AccessToken
     */
    public function refreshAccessToken(array $params = [])
    {
        $params = array_merge([
            'grant_type'    => \ITS\Trustpilot\API\OAuth2\GrantType::TYPE_REFRESH_TOKEN,
            'refresh_token' => $this->getClient()->getAccessToken()->getRefreshToken()
        ], $params);

        $result = $this->getClient()->post(
            $this->getRoute(__FUNCTION__),
            $params,
            [
                'auth' => [$this->getClient()->getGrantType()->getApiKey(), $this->getClient()->getGrantType()->getApiSecret()]
            ]
        );

        return $o = new \ITS\Trustpilot\API\OAuth2\AccessToken(
            $result->access_token,
            $result->refresh_token,
            new \DateTime('@' . (time() + $result->expires_in))
        );
    }

    /**
     * @link https://developers.trustpilot.com/authentication
     *
     * @param array $params
     *
     * @throws \Exception
     * @return null|\StdClass
     */
    public function revokeAccessToken(array $params = [])
    {
        $params = array_merge([
            'token' => $this->getClient()->getAccessToken()->getRefreshToken()
        ], $params);

        return $this->getClient()->post($this->getRoute(__FUNCTION__), $params);
    }
}
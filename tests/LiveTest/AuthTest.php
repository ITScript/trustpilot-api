<?php

namespace ITS\Trustpilot\LiveTest;

use ITS\Trustpilot\API\HttpClient;
use ITS\Trustpilot\API\OAuth2\AccessToken;
use ITS\Trustpilot\API\OAuth2\GrantType;
use ITS\Trustpilot\API\OAuth2\GrantType\PasswordGrantType;
use ITS\Trustpilot\BaseTest;

class AuthTest extends BaseTest
{
    public function dataGrantType()
    {
        $apiKey    = self::getEnvApiKey();
        $apiSecret = self::getEnvApiSecret();
        $username  = self::getEnvUsername();
        $password  = self::getEnvPassword();

        yield [new PasswordGrantType($apiKey, $apiSecret, $username, $password)];
    }

    /**
     * @covers OAuth2::obtainAccessToken
     * @covers OAuth2::refreshAccessToken
     * @covers OAuth2::revokeAccessToken
     * @dataProvider dataGrantType
     * @param \ITS\Trustpilot\API\OAuth2\GrantType $grantType
     */
    public function testAuth(GrantType $grantType)
    {
        $client = new HttpClient($grantType);

        $token = $client->oauth2()->obtainAccessToken();

        $this->assertInstanceOf(AccessToken::class, $token);

        $this->assertNotEmpty($token->getValue());

        $refreshedToken = $client->oauth2()->refreshAccessToken([
            'refresh_token' => $token->getRefreshToken(),
        ]);

        $this->assertInstanceOf(AccessToken::class, $refreshedToken);

        $this->assertNotEmpty($refreshedToken->getValue());

        $this->assertNotEquals($token->getValue(), $refreshedToken->getValue());

        $this->assertGreaterThanOrEqual($token->getExpiryDate(), $refreshedToken->getExpiryDate());

        $client->oauth2()->revokeAccessToken([
            'token' => $refreshedToken->getRefreshToken(),
        ]);

        $this->expectExceptionCode(404);

        $client->oauth2()->revokeAccessToken([
            'token' => $token->getRefreshToken(),
        ]);
    }
}
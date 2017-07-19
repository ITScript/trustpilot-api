<?php

namespace ITS\Trustpilot\LiveTest;

use ITS\Trustpilot\API\HttpClient;
use ITS\Trustpilot\API\OAuth2\GrantType\PasswordGrantType;
use ITS\Trustpilot\BaseTest;

class BusinessUnitTest extends BaseTest
{
    /** @var  HttpClient */
    protected $client;

    public function setUp()
    {
        $apiKey    = self::getEnvApiKey();
        $apiSecret = self::getEnvApiSecret();
        $username  = self::getEnvUsername();
        $password  = self::getEnvPassword();

        $this->client = new HttpClient(new PasswordGrantType($apiKey, $apiSecret, $username, $password));
    }

    /**
     * @covers \ITS\Trustpilot\API\Endpoint\BusinessUnit::getInfo
     */
    public function testGetInfo()
    {
        $result = $this->client->businessUnit(self::getEnvBusinessUnitId())->getInfo();

        $this->assertAttributeNotEmpty('status', $result);
        $this->assertAttributeNotEmpty('stars', $result);
        $this->assertAttributeEquals(self::getEnvBusinessUnitId(), 'id', $result);
    }
}

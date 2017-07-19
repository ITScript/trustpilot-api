<?php

namespace ITS\Trustpilot\LiveTest;

use ITS\Trustpilot\API\HttpClient;
use ITS\Trustpilot\API\OAuth2\GrantType\PasswordGrantType;
use ITS\Trustpilot\BaseTest;

class ResourceTest extends BaseTest
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
     * @return array
     */
    public function dataStars()
    {
        return [[0], [1], [2], [3], [4], [5]];
    }

    /**
     * @covers \ITS\Trustpilot\API\Endpoint\Resource::getStarImages
     *
     * @dataProvider dataStars
     */
    public function testGetStarImages($stars)
    {
        $result = $this->client->resource()->getStarImages($stars);

        $this->assertAttributeNotEmpty('image520x96', $result);
        $this->assertAttributeNotEmpty('image130x24', $result);
        $this->assertAttributeNotEmpty('image260x48', $result);
        $this->assertAttributeEquals($stars, 'stars', $result);
    }

    /**
     * @covers \ITS\Trustpilot\API\Endpoint\Resource::getStarString
     *
     * @dataProvider dataStars
     */
    public function testGetStarString($stars)
    {
        $result = $this->client->resource()->getStarString($stars, self::getLocale());

        $this->assertAttributeNotEmpty('string', $result);
        $this->assertAttributeEquals($stars, 'stars', $result);
        $this->assertAttributeEquals(self::getLocale(), 'locale', $result);
    }
}

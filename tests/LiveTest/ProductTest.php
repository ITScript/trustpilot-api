<?php

namespace ITS\Trustpilot\LiveTest;

use ITS\Trustpilot\API\HttpClient;
use ITS\Trustpilot\API\OAuth2\GrantType\PasswordGrantType;
use ITS\Trustpilot\BaseTest;

class ProductTest extends BaseTest
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

    public function dataFindAll()
    {
        yield [['perPage' => 5]];
    }

    /**
     * @covers Product::findAll
     * @dataProvider dataFindAll
     * @param array $params
     */
    public function testFindAll(array $params)
    {
        $result = $this->client->products(self::getEnvBusinessUnitId())->findAll($params);

        $this->assertAttributeNotEmpty('products', $result);
    }
}

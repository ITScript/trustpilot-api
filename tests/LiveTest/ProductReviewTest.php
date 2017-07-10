<?php

namespace ITS\Trustpilot\LiveTest;

use ITS\Trustpilot\API\HttpClient;
use ITS\Trustpilot\API\OAuth2\GrantType\PasswordGrantType;
use ITS\Trustpilot\BaseTest;

class ProductReviewTest extends BaseTest
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

    public function dataCreateInvitationLink()
    {
        yield [
            [
                'referenceId' => 'TEST',
                'locale'      => 'en-GB',
                'consumer'    => [
                    'email' => 'dev@acme.com',
                    'name'  => 'dev acme'
                ],
                'products' => [
                    [
                        'sku'        => 'test-sku',
                        'name'       => 'test-name',
                        'imageUrl'   => 'http://lorempixel.com/500/500/cats/',
                        'productUrl' => 'http://acme.com/test-url'
                    ]
                ],
            ],

        ];
    }

    /**
     * @covers       ProductReview::createInvitationLink
     * @dataProvider dataCreateInvitationLink
     * @param array $params
     */
    public function testCreateInvitationLink(array $params)
    {
        $result = $this->client->productReviews(self::getEnvBusinessUnitId())->createInvitationLink($params);

        $this->assertAttributeNotEmpty('reviewLinkId', $result);
        $this->assertAttributeNotEmpty('reviewUrl', $result);
    }
}

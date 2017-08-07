<?php

namespace ITS\Trustpilot\LiveTest;

use ITS\Trustpilot\API\HttpClient;
use ITS\Trustpilot\API\OAuth2\GrantType\PasswordGrantType;
use ITS\Trustpilot\BaseTest;

class ProductReviewTest extends BaseTest
{
    /** @var  HttpClient */
    protected $client;

    /**
     * @return void
     */
    public function setUp()
    {
        $apiKey    = self::getEnvApiKey();
        $apiSecret = self::getEnvApiSecret();
        $username  = self::getEnvUsername();
        $password  = self::getEnvPassword();

        $this->client = new HttpClient(new PasswordGrantType($apiKey, $apiSecret, $username, $password));
    }

    /**
     * @return \Generator
     */
    public function dataCreateInvitationLink()
    {
        yield [
            [
                'referenceId' => 'TEST',
                'locale'      => 'en-GB',
                'consumer'    => [
                    'email' => 'dev@example.com',
                    'name'  => 'Firstname Lastname'
                ],
                'products' => [
                    [
                        'sku'        => 'test-sku',
                        'name'       => 'test-name',
                        'imageUrl'   => getenv('PRODUCT_IMAGE_URL'),
                        'productUrl' => getenv('PRODUCT_PAGE_URL'),
                    ]
                ],
            ],

        ];
    }

    /**
     * @covers       ProductReview::createInvitationLink
     * @dataProvider dataCreateInvitationLink
     * @param array  $params
     */
    public function testCreateInvitationLink(array $params)
    {
        $result = $this->client->productReviews(self::getEnvBusinessUnitId())->createInvitationLink($params);

        $this->assertAttributeNotEmpty('reviewLinkId', $result);
        $this->assertAttributeNotEmpty('reviewUrl', $result);
    }
}

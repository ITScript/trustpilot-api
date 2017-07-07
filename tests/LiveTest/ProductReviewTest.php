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
                    'email' => 'dev@itscript.com',
                    'name'  => 'Dev ITScript'
                ],
                'products' => [
                    [
                        'sku'        => 'V_TestOnlyThreePieceSet',
                        'name'       => 'Test Only Maderno Freestanding Bath 1710 x 770mm',
                        'imageUrl'   => 'http://www.bathrooms.com/images/p/freestanding_baths/F-210/F-210+Mars_Freestanding_Bath-bathrooms_com-scene-square-medium-white.jpg',
                        'productUrl' => 'http://www.bathrooms.com/testonlythreepieceset'
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

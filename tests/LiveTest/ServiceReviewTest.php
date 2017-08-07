<?php

namespace ITS\Trustpilot\LiveTest;

use ITS\Trustpilot\API\HttpClient;
use ITS\Trustpilot\API\OAuth2\GrantType\PasswordGrantType;
use ITS\Trustpilot\BaseTest;

class ServiceReviewTest extends BaseTest
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
                'email'       => 'dev@example.com',
                'name'        => 'Firstname Lastname'
            ]
        ];
    }

    /**
     * @covers       ServiceReview::createInvitationLink
     * @dataProvider dataCreateInvitationLink
     * @param array  $params
     */
    public function testCreateInvitationLink(array $params)
    {
        $result = $this->client->serviceReviews(self::getEnvBusinessUnitId())->createInvitationLink($params);

        $this->assertAttributeNotEmpty('url', $result);
        $this->assertAttributeNotEmpty('id', $result);
    }
}

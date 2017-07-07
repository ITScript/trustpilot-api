<?php

namespace ITS\Trustpilot\LiveTest;

use ITS\Trustpilot\API\HttpClient;
use ITS\Trustpilot\API\OAuth2\GrantType\PasswordGrantType;
use ITS\Trustpilot\BaseTest;

class ServiceReviewTest extends BaseTest
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
     * @covers ServiceReview::createInvitationLink
     */
    public function testCreateInvitationLink()
    {
        $this->markTestIncomplete();

    }
}

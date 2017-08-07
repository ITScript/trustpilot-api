<?php

namespace ITS\Trustpilot\API\Endpoint;

use ITS\Trustpilot\API\Endpoint;
use ITS\Trustpilot\API\HttpClient;
use ITS\Trustpilot\API\Cypher;

class ServiceReview extends Endpoint
{
    /**
     * @var string
     */
    protected $businessUnitId;

    /**
     * @var string
     */
    protected $apiDomain = 'invitations-api.trustpilot.com';

    /**
     * Invitation constructor.
     * @param HttpClient $client
     * @param string     $businessUnitId
     */
    public function __construct(HttpClient $client, $businessUnitId)
    {
        parent::__construct($client);

        $this->businessUnitId = $businessUnitId;
    }

    /**
     * Declares routes to be used by this resource.
     */
    protected function setUpRoutes()
    {
        parent::setUpRoutes();
        $this->setRoutes([
            'createInvitationLink' => 'private/business-units/{businessUnitId}/invitation-links?token={token}',
        ]);
    }

    /**
     * @link https://developers.trustpilot.com/invitation-api#generate-service-review-invitation-link
     *
     * @param array $params
     *
     * @throws \Exception
     * @return null|\stdClass
     */
    public function createInvitationLink(array $params = [])
    {
        $this->setAdditionalRouteParams([
            'businessUnitId' => $this->businessUnitId,
            'token'          => $this->getClient()->getAccessToken()->getValue()
        ]);

        return $result = $this->getClient()->post($this->getRoute(__FUNCTION__), $params);
    }

    /**
     * @link https://support.trustpilot.com/hc/en-us/articles/115004145087--Business-Generated-Links-for-developers-
     *
     * @param array $params
     *
     * @return string
     */
    public function createBusinessGeneratedLink(array $params = [])
    {
        $payload = Cypher::encryptPayload($params['payload'], $params['encrypt_key'], $params['auth_key']);
        $domain  = $params['domain'];

        if (!empty($params['embed']) && $params['embed']) {
            return "https://www.trustpilot.com/evaluate-bgl/embed/{$domain}?p={$payload}";
        }

        return "https://www.trustpilot.com/evaluate-bgl/{$domain}?p={$payload}";
    }
}
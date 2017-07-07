<?php

namespace ITS\Trustpilot\API\Endpoint;

class ServiceReview extends \ITS\Trustpilot\API\Endpoint
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
     * @param \ITS\Trustpilot\API\HttpClient $client
     * @param string                         $businessUnitId
     */
    public function __construct(\ITS\Trustpilot\API\HttpClient $client, $businessUnitId)
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
     *  Example of request
     *  {
     *      "referenceId": "inv00001",
     *      "name": "John Doe",
     *      "tags": [
     *          "tag1",
     *          "tag2"
     *      ],
     *      "locale": "en-US",
     *      "email": "john.doe@trustpilot.com",
     *      "redirectUri": "http://trustpilot.com"
     *  }
     *
     *  Example of response
     *  {
     *      "url": "https://www.trustpilot.com/evaluate-link/9ee95945f2aa3a748301efdc6b8b51d7",
     *      "id": "9ee95945f2aa3a748301efdc6b8b51d7"
     *  }
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
}
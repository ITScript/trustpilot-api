<?php

namespace ITS\Trustpilot\API\Endpoint;

class ProductReview extends \ITS\Trustpilot\API\Endpoint
{
    /**
     * @var string
     */
    protected $businessUnitId;

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
            'createInvitationLink' => 'private/product-reviews/business-units/{businessUnitId}/invitation-links?token={token}',
            'findPrivate'          => 'private/product-reviews/business-units/{businessUnitId}/reviews?token={token}'
        ]);
    }

    /**
     * @link https://developers.trustpilot.com/product-reviews-api#create-product-review-invitation-link
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

        return $this->getClient()->post($this->getRoute(__FUNCTION__), $params);
    }

    /**
     * @link https://developers.trustpilot.com/product-reviews-api#get-private-product-reviews
     *
     * @param array $params
     *
     * @throws \Exception
     * @return null|\stdClass
     */
    public function findPrivate(array $params = [])
    {
        $this->setAdditionalRouteParams([
            'businessUnitId' => $this->businessUnitId,
            'token'          => $this->getClient()->getAccessToken()->getValue()
        ]);

        return $this->getClient()->get($this->getRoute(__FUNCTION__), $params);
    }
}
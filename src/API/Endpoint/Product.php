<?php

namespace ITS\Trustpilot\API\Endpoint;

class Product extends \ITS\Trustpilot\API\Endpoint
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
            'findAll' => 'private/business-units/{businessUnitId}/products?token={token}',
        ]);
    }

    /**
     * @link https://developers.trustpilot.com/products-api#get-products
     *
     * @param array $params
     *
     * @throws \Exception
     * @return null|\stdClass
     */
    public function findAll(array $params = [])
    {
        $this->setAdditionalRouteParams([
            'businessUnitId' => $this->businessUnitId,
            'token'          => $this->getClient()->getAccessToken()->getValue()
        ]);

        return $this->getClient()->get($this->getRoute(__FUNCTION__), $params);
    }
}
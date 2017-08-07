<?php

namespace ITS\Trustpilot\API\Endpoint;

use ITS\Trustpilot\API\Endpoint;
use ITS\Trustpilot\API\HttpClient;

class Product extends Endpoint
{
    /**
     * @var string
     */
    protected $businessUnitId;

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
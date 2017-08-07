<?php

namespace ITS\Trustpilot\API\Endpoint;

use ITS\Trustpilot\API\Endpoint;
use ITS\Trustpilot\API\HttpClient;

class BusinessUnit extends Endpoint
{
    /**
     * @var string
     */
    protected $businessUnitId;

    /**
     * BusinessUnit constructor.
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
            'getInfo' => 'business-units/{businessUnitId}?apikey={apikey}',
        ]);
    }

    /**
     * @link https://developers.trustpilot.com/business-unit-api#get-public-business-unit
     *
     * @param array $params
     *
     * @throws \Exception
     * @return null|\stdClass
     */
    public function getInfo(array $params = [])
    {
        $this->setAdditionalRouteParams([
            'businessUnitId' => $this->businessUnitId,
            'apikey'         => $this->getClient()->getGrantType()->getApiKey(),
        ]);

        return $result = $this->getClient()->get($this->getRoute(__FUNCTION__), $params);
    }
}
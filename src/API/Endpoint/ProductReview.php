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
        ]);
    }

    /**
     *  Example of request
     *  {
     *      "referenceId": "123ABC",
     *      "locale": "en-US",
     *      "products": [
     *          {
     *              "sku": "ABC-1234",
     *              "name": "Metal Toy Car",
     *              "mpn": "7TX1641",
     *              "imageUrl": "http://www.mycompanystore.com/products/images/12345.jpg",
     *              "productUrl": "http://www.mycompanystore.com/products/12345.htm",
     *              "gtin": "01234567890",
     *              "brand": "Acme"
     *          },
     *          ...
     *      ],
     *      "consumer": {
     *          "email": "johndoe@somewhere.com",
     *          "name": "John Doe"
     *      },
     *      "redirectUri": "https://www.example.com"
     *  }
     *
     *  Example of response
     *  {
     *      "reviewUrl": "https://products.trustpilot.com/evaluate/i377afKxa7abY",
     *      "reviewLinkId": "i9tafKx7abY"
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

        return $this->getClient()->post($this->getRoute(__FUNCTION__), $params);;
    }
}
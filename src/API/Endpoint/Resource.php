<?php

namespace ITS\Trustpilot\API\Endpoint;

class Resource extends \ITS\Trustpilot\API\Endpoint
{
    /**
     * Declares routes to be used by this resource.
     */
    protected function setUpRoutes()
    {
        parent::setUpRoutes();

        $this->setRoutes([
            'getStarImages' => 'resources/images/stars/{stars}?apikey={apikey}',
            'getStarString' => 'resources/strings/stars/{stars}?locale={locale}&apikey={apikey}',
        ]);
    }

    /**
     * @link https://developers.trustpilot.com/resources-api#get-the-star-image-resources
     *
     * @param int $stars
     *
     * @throws \Exception
     * @return null|\stdClass
     */
    public function getStarImages($stars)
    {
        $this->setAdditionalRouteParams([
            'apikey' => $this->getClient()->getGrantType()->getApiKey(),
            'stars'  => $stars,
        ]);

        return $result = $this->getClient()->get($this->getRoute(__FUNCTION__));
    }

    /**
     * @link https://developers.trustpilot.com/resources-api#get-the-string-representation-of-the-stars
     *
     * @param int    $stars
     * @param string $locale
     *
     * @return null|\stdClass
     */
    public function getStarString($stars, $locale)
    {
        $this->setAdditionalRouteParams([
            'apikey' => $this->getClient()->getGrantType()->getApiKey(),
            'stars'  => $stars,
            'locale' => $locale
        ]);

        return $result = $this->getClient()->get($this->getRoute(__FUNCTION__));
    }
}
<?php

namespace ITS\Trustpilot\API;

/**
 * Abstract class for all endpoints
 *
 */
abstract class Endpoint
{
    /**
     * @var String
     */
    protected $itemName;

    /**
     * @var string This is appended between the full base domain and the resource endpoint
     */
    protected $apiBasePath = 'v1/';

    /**
     * @var string
     */
    protected $apiDomain = 'api.trustpilot.com';

    /**
     * @var string
     */
    protected $apiScheme = 'https';

    /**
     * @var integer
     */
    protected $apiPort = 443;

    /**
     * @var \ITS\Trustpilot\API\HttpClient
     */
    protected $client;

    /**
     * @var int
     */
    protected $lastId;

    /**
     * @var array
     */
    protected $routes = [];

    /**
     * @var array
     */
    protected $additionalRouteParams = [];

    /**
     * @param \ITS\Trustpilot\API\HttpClient $client
     */
    public function __construct(\ITS\Trustpilot\API\HttpClient $client)
    {
        $this->client = $client;

        if (! isset($this->itemName)) {
            $this->itemName = $this->getItemNameFromClass();
        }

        $this->setUpRoutes();
    }

    /**
     * Returns the generated API URL
     *
     * @return string
     */
    public function getApiURL()
    {
        return "{$this->apiScheme}://{$this->apiDomain}:{$this->apiPort}/{$this->apiBasePath}";
    }

    /**
     * @return \ITS\Trustpilot\API\HttpClient
     */
    public function getClient()
    {
        $this->client->setApiUrl($this->getApiURL());
        $this->client->setHeader('Content-Type', 'application/x-www-form-urlencoded');

        return $this->client;
    }

    /**
     * Return the item name using the name of the class (used for endpoints)
     *
     * @return string
     */
    protected function getItemNameFromClass()
    {
        $namespacedClassName = get_class($this);
        $itemName            = join('', array_slice(explode('\\', $namespacedClassName), -1));

        $underscored = strtolower(preg_replace('/(?<!^)([A-Z])/', '_$1', $itemName));

        return strtolower($underscored);
    }

    /**
     * @return String
     */
    public function getItemName()
    {
        return $this->itemName;
    }

    /**
     * Sets up the available routes for the item.
     */
    protected function setUpRoutes()
    {
    }

    /**
     * Saves an id for future methods in the chain
     *
     * @param int $id
     *
     * @return $this
     */
    public function setLastId($id)
    {
        $this->lastId = $id;

        return $this;
    }

    /**
     * Saves an id for future methods in the chain
     *
     * @return int
     */
    public function getLastId()
    {
        return $this->lastId;
    }

    /**
     * Check that all parameters have been supplied
     *
     * @param array $params
     * @param array $mandatory
     *
     * @return bool
     */
    public function hasKeys(array $params, array $mandatory)
    {
        for ($i = 0; $i < count($mandatory); $i++) {
            if (! array_key_exists($mandatory[$i], $params)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check that any parameter has been supplied
     *
     * @param array $params
     * @param array $mandatory
     *
     * @return bool
     */
    public function hasAnyKey(array $params, array $mandatory)
    {
        for ($i = 0; $i < count($mandatory); $i++) {
            if (array_key_exists($mandatory[$i], $params)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Wrapper for adding multiple routes via setRoute
     *
     * @param array $routes
     */
    public function setRoutes(array $routes)
    {
        foreach ($routes as $name => $route) {
            $this->setRoute($name, $route);
        }
    }

    /**
     * Add or override an existing route
     *
     * @param $name
     * @param $route
     */
    public function setRoute($name, $route)
    {
        $this->routes[$name] = $route;
    }

    /**
     * Return all routes for this item
     *
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Returns a route and replaces tokenized parts of the string with
     * the passed params
     *
     * @param       $name
     * @param array $params
     *
     * @return mixed
     * @throws \Exception
     */
    public function getRoute($name, array $params = [])
    {
        if (! isset($this->routes[$name])) {
            throw new \Exception('Route not found.');
        }

        $route  = $this->routes[$name];
        $params = array_merge($params, $this->getAdditionalRouteParams());

        foreach ($params as $name => $value) {
            if (is_scalar($value)) {
                $route = str_replace('{' . $name . '}', $value, $route);
            }
        }

        return $route;
    }

    /**
     * @param array $additionalRouteParams
     */
    public function setAdditionalRouteParams(array $additionalRouteParams)
    {
        $this->additionalRouteParams = $additionalRouteParams;
    }

    /**
     * @return array
     */
    public function getAdditionalRouteParams()
    {
        return $this->additionalRouteParams;
    }
}
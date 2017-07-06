<?php

namespace ITS\Trustpilot\API;

/**
 * Client class, base level access
 *
 * @method \ITS\Trustpilot\API\Endpoint\OAuth2 oauth2()
 *
 */
class HttpClient
{
    const VERSION = '1.0.0';

    /**
     * @var array $headers
     */
    private $headers = [];

    /**
     * @var string
     */
    protected $apiUrl = null;

    /**
     * @var \GuzzleHttp\Client
     */
    protected $guzzle;

    /**
     * @var \ITS\Trustpilot\API\HttpDebug
     */
    protected $debug;

    /**
     * @var \ITS\Trustpilot\API\OAuth2\GrantType
     */
    protected $grantType;

    /**
     * @var \ITS\Trustpilot\API\OAuth2\AccessToken
     */
    protected $accessToken;

    /**
     * @var array
     */
    protected $endpoint_map = [
        'oauth2' => \ITS\Trustpilot\API\Endpoint\OAuth2::class,
    ];

    /**
     * HttpClient constructor.
     * @param \ITS\Trustpilot\API\OAuth2\GrantType $grantType
     */
    public function __construct(\ITS\Trustpilot\API\OAuth2\GrantType $grantType = null)
    {
        $this->guzzle = new \GuzzleHttp\Client();
        $this->debug  = new \ITS\Trustpilot\API\HttpDebug();

        $this->grantType = $grantType;
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return \ITS\Trustpilot\API\Endpoint
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        if ((array_key_exists($name, $this->endpoint_map))) {
            /** @var \ITS\Trustpilot\API\Endpoint $className */
            $className = $this->endpoint_map[$name];
            $class     = new $className($this);
        } else {
            throw new \Exception("No method called $name available in " . __CLASS__);
        }

        return $class;
    }

    /**
     * @return \ITS\Trustpilot\API\OAuth2\GrantType
     */
    public function getGrantType()
    {
        return $this->grantType;
    }

    /**
     * @return \ITS\Trustpilot\API\OAuth2\AccessToken
     */
    public function getAccessToken()
    {
        if (is_null($this->accessToken)) {
            $this->accessToken = $this->oauth2()->obtainAccessToken();
        } elseif ($this->accessToken->hasExpired()) {
            $this->accessToken = $this->oauth2()->refreshAccessToken();
        }

        return $this->accessToken;
    }

    /**
     * @param \ITS\Trustpilot\API\OAuth2\AccessToken|null $accessToken
     * @return $this
     */
    public function setAccessToken(\ITS\Trustpilot\API\OAuth2\AccessToken $accessToken = null)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param string $key The name of the header to set
     * @param string $value The value to set in the header
     * @return HttpClient
     * @internal param array $headers
     *
     */
    public function setHeader($key, $value)
    {
        $this->headers[$key] = $value;

        return $this;
    }

    /**
     * Return the user agent string
     *
     * @return string
     */
    public function getUserAgent()
    {
        return 'TrustpilotAPI PHP ' . self::VERSION;
    }

    /**
     * @param string $apiUrl The generated API URL
     * @return $this
     */
    public function setApiUrl($apiUrl)
    {
        $this->apiUrl = $apiUrl;

        return $this;
    }

    /**
     * Returns the generated api URL
     *
     * @return string
     */
    public function getApiUrl()
    {
        return $this->apiUrl;
    }

    /**
     * Returns debug information in an object
     *
     * @return \ITS\Trustpilot\API\HttpDebug
     */
    public function getDebug()
    {
        return $this->debug;
    }

    /**
     * Set debug information as an object
     *
     * @param \GuzzleHttp\Psr7\Request|null $req
     * @param \GuzzleHttp\Psr7\Response|null $res
     * @param \Exception|null $e
     */
    public function debug(\GuzzleHttp\Psr7\Request $req = null, \GuzzleHttp\Psr7\Response $res = null, \Exception $e = null)
    {
        $this->debug->lastRequestHeaders  = $req->getHeaders();
        $this->debug->lastRequestBody     = $req->getBody();
        $this->debug->lastResponseCode    = isset($res) ? $res->getStatusCode() : null;
        $this->debug->lastResponseHeaders = isset($res) ? $res->getHeaders() : null;
        $this->debug->lastResponseError   = isset($e) ? $e : null;
    }

    /**
     * This is a helper method to do a get request.
     *
     * @param       $endpoint
     * @param array $queryParams
     *
     * @return \stdClass|null
     * @throws \Exception
     */
    public function get($endpoint, $queryParams = [])
    {
        return $this->makeRequest($endpoint, ['queryParams' => $queryParams]);
    }

    /**
     * This is a helper method to do a post request.
     *
     * @param       $endpoint
     * @param array $postData
     *
     * @param array $options
     * @return null|\stdClass
     * @throws \Exception
     */
    public function post($endpoint, $postData = [], $options = [])
    {
        $extraOptions = array_merge($options, [
            'postFields' => $postData,
            'method'     => 'POST'
        ]);

        return $this->makeRequest($endpoint, $extraOptions);
    }

    /**
     * This is a helper method to do a put request.
     *
     * @param       $endpoint
     * @param array $putData
     *
     * @return \stdClass | null
     * @throws \Exception
     */
    public function put($endpoint, $putData = [])
    {
        return $this->makeRequest($endpoint, ['postFields' => $putData, 'method' => 'PUT']);
    }

    /**
     * This is a helper method to do a delete request.
     *
     * @param $endpoint
     *
     * @return null
     * @throws \Exception
     */
    public function delete($endpoint)
    {
        return $this->makeRequest($endpoint, ['method' => 'DELETE']);
    }

    /**
     * @param string $endPoint
     * @param array  $options
     * @return mixed
     */
    protected function makeRequest($endPoint, $options = [])
    {
        $options = array_merge(
            [
                'method'      => 'GET',
                'contentType' => 'application/json',
                'postFields'  => null,
                'queryParams' => null
            ],
            $options
        );

        $headers = array_merge([
            'Accept'       => 'application/json',
            'Content-Type' => $options['contentType'],
            'User-Agent'   => $this->getUserAgent()
        ], $this->getHeaders());

        /** @var \GuzzleHttp\Psr7\Request $request */
        $request = new \GuzzleHttp\Psr7\Request($options['method'], $this->getApiUrl() . $endPoint, $headers);

        /** @var \GuzzleHttp\Psr7\Response $response */
        $response = null;

        $requestOptions = [];

        if (! empty($options['multipart'])) {
            $request                     = $request->withoutHeader('Content-Type');
            $requestOptions['multipart'] = $options['multipart'];
        } elseif (! empty($options['postFields'])) {
            if ($headers['Content-Type'] == 'application/json') {
                $resource = json_encode($options['postFields']);
            } else {
                $resource = http_build_query($options['postFields'], '', '&');
            }

            $request = $request->withBody(\GuzzleHttp\Psr7\stream_for($resource));
        } elseif (! empty($options['file'])) {
            if ($options['file'] instanceof \Psr\Http\Message\StreamInterface) {
                $request = $request->withBody($options['file']);
            } elseif (is_file($options['file'])) {
                $fileStream = new \GuzzleHttp\Psr7\LazyOpenStream($options['file'], 'r');
                $request    = $request->withBody($fileStream);
            }
        }

        if (! empty($options['queryParams'])) {
            foreach ($options['queryParams'] as $queryKey => $queryValue) {
                $uri     = $request->getUri();
                $uri     = $uri->withQueryValue($uri, $queryKey, $queryValue);
                $request = $request->withUri($uri, true);
            }
        }

        if (! empty($options['auth'])) {
            $requestOptions['auth'] = $options['auth'];
        }

        try {
            $response = $this->guzzle->send($request, $requestOptions);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            throw \GuzzleHttp\Exception\RequestException::create($e->getRequest(), $e->getResponse(), $e);
        } finally {
            $this->debug($request, $response, isset($e) ? $e : null);

            $request->getBody()->rewind();
        }

        return json_decode($response->getBody());
    }
}
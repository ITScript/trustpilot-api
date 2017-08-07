<?php

namespace ITS\Trustpilot\API;

use GuzzleHttp\Psr7\LazyOpenStream;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;
use Psr\Http\Message\StreamInterface;
use ITS\Trustpilot\API\Endpoint\OAuth2;
use ITS\Trustpilot\API\Endpoint\ServiceReview;
use ITS\Trustpilot\API\Endpoint\ProductReview;
use ITS\Trustpilot\API\Endpoint\Product;
use ITS\Trustpilot\API\Endpoint\BusinessUnit;
use ITS\Trustpilot\API\Endpoint\Resource;
use ITS\Trustpilot\API\OAuth2\AccessToken;
use ITS\Trustpilot\API\OAuth2\GrantType;

/**
 * Client class, base level access
 *
 * @method OAuth2 oauth2()
 * @method ServiceReview serviceReviews(string $businessUnitId)
 * @method ProductReview productReviews(string $businessUnitId)
 * @method Product products(string $businessUnitId)
 * @method BusinessUnit businessUnit(string $businessUnitId)
 * @method Resource resource()
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
     * @var Client
     */
    protected $guzzle;

    /**
     * @var HttpDebug
     */
    protected $debug;

    /**
     * @var GrantType
     */
    protected $grantType;

    /**
     * @var AccessToken
     */
    protected $accessToken;

    /**
     * @var array
     */
    protected $endpoint_map = [
        'oauth2'         => OAuth2::class,
        'serviceReviews' => ServiceReview::class,
        'productReviews' => ProductReview::class,
        'products'       => Product::class,
        'businessUnit'   => BusinessUnit::class,
        'resource'       => Resource::class,
    ];

    /**
     * HttpClient constructor.
     * @param GrantType $grantType
     */
    public function __construct(GrantType $grantType = null)
    {
        $this->guzzle = new Client();
        $this->debug  = new HttpDebug();

        $this->grantType = $grantType;
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return object
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        if ((array_key_exists($name, $this->endpoint_map))) {
            $endpoint  = new \ReflectionClass($this->endpoint_map[$name]);
            $arguments = array_merge([$this], $arguments);

            return $endpoint->newInstanceArgs($arguments);
        } else {
            throw new \Exception("No method called $name available in " . __CLASS__);
        }
    }

    /**
     * @return GrantType
     */
    public function getGrantType()
    {
        return $this->grantType;
    }

    /**
     * @return AccessToken
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
     * @param AccessToken|null $accessToken
     * @return $this
     */
    public function setAccessToken(AccessToken $accessToken = null)
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
     * @return HttpDebug
     */
    public function getDebug()
    {
        return $this->debug;
    }

    /**
     * Set debug information as an object
     *
     * @param Request|null $req
     * @param Response|null $res
     * @param \Exception|null $e
     */
    public function debug(Request $req = null, Response $res = null, \Exception $e = null)
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

        /** @var Request $request */
        $request = new Request($options['method'], $this->getApiUrl() . $endPoint, $headers);

        /** @var Response $response */
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
            if ($options['file'] instanceof StreamInterface) {
                $request = $request->withBody($options['file']);
            } elseif (is_file($options['file'])) {
                $fileStream = new LazyOpenStream($options['file'], 'r');
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
        } catch (RequestException $e) {
            throw RequestException::create($e->getRequest(), $e->getResponse(), $e);
        } finally {
            $this->debug($request, $response, isset($e) ? $e : null);

            $request->getBody()->rewind();
        }

        return json_decode($response->getBody());
    }
}
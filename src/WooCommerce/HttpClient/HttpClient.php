<?php
/**
 * WooCommerce REST API HTTP Client
 *
 * @category HttpClient
 * @package  Automattic/WooCommerce
 */

namespace Automattic\WooCommerce\HttpClient;

use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\OAuth;
use Automattic\WooCommerce\HttpClient\Request;
use Automattic\WooCommerce\HttpClient\Response;
use Automattic\WooCommerce\HttpClient\HttpClientException;

/**
 * REST API HTTP Client class.
 *
 * @package Automattic/WooCommerce
 */
class HttpClient
{

    /**
     * Default WooCommerce REST API version.
     */
    const VERSION = 'v3';

    /**
     * Default request timeout.
     */
    const TIMEOUT = 15;

    /**
     * cURL handle.
     *
     * @var resource
     */
    protected $ch;

    /**
     * Store API URL.
     *
     * @var string
     */
    protected $url;

    /**
     * Consumer key.
     *
     * @var string
     */
    protected $consumerKey;

    /**
     * Consumer secret.
     *
     * @var string
     */
    protected $consumerSecret;

    /**
     * WooCommerce REST API version.
     *
     * @var string
     */
    protected $version;

    /**
     * If is under SSL.
     *
     * @var bool
     */
    protected $isSsl;

    /**
     * If need verify SSL.
     *
     * @var bool
     */
    protected $verifySsl;

    /**
     * Requests timeout.
     *
     * @var int
     */
    protected $timeout;

    /**
     * Basic authentication as query string.
     *
     * @var bool
     */
    protected $queryStringAuth;

    /**
     * Request.
     *
     * @var Request
     */
    private $request;

    /**
     * Response.
     *
     * @var Response
     */
    private $response;

    /**
     * Response headers.
     *
     * @var string
     */
    private $responseHeaders;

    /**
     * Initialize HTTP client.
     *
     * @param string $url            Store URL.
     * @param string $consumerKey    Consumer key.
     * @param string $consumerSecret Consumer Secret.
     * @param array  $options        Client options.
     */
    public function __construct($url, $consumerKey, $consumerSecret, $options)
    {
        if (!\function_exists('curl_version')) {
            throw new HttpClientException('cURL is NOT installed on this server', -1, new Request(), new Response());
        }

        $this->version         = $this->getVersion($options);
        $this->isSsl           = $this->isSsl($url);
        $this->url             = $this->buildApiUrl($url);
        $this->consumerKey     = $consumerKey;
        $this->consumerSecret  = $consumerSecret;
        $this->verifySsl       = $this->verifySsl($options);
        $this->timeout         = $this->getTimeout($options);
        $this->queryStringAuth = $this->isQueryStringAuth($options);
    }

    /**
     * Get API version.
     *
     * @param array $options Client options.
     *
     * @return string
     */
    protected function getVersion($options)
    {
        return isset($options['version']) ? $options['version'] : self::VERSION;
    }

    /**
     * Check if is under SSL.
     *
     * @param string $url Store URL.
     *
     * @return bool
     */
    protected function isSsl($url)
    {
        return 'https://' === \substr($url, 0, 8);
    }

    /**
     * Build API URL.
     *
     * @param string $url Store URL.
     *
     * @return string
     */
    protected function buildApiUrl($url)
    {
        return \rtrim($url, '/') . '/wc-api/' . $this->version . '/';
    }

    /**
     * Check if need to verify SSL.
     *
     * @param array $options Client options.
     *
     * @return bool
     */
    protected function verifySsl($options)
    {
        return isset($options['verify_ssl']) ? (bool) $options['verify_ssl'] : true;
    }

    /**
     * Get timeout.
     *
     * @param array $options Client options.
     *
     * @return int
     */
    protected function getTimeout($options)
    {
        return isset($options['timeout']) ? (int) $options['timeout'] : self::TIMEOUT;
    }

    /**
     * Basic Authentication as query string.
     * Some old servers are not able to use CURLOPT_USERPWD.
     *
     * @param array $options Client options.
     *
     * @return bool
     */
    protected function isQueryStringAuth($options)
    {
        return isset($options['query_string_auth']) ? (bool) $options['query_string_auth'] : false;
    }

    /**
     * Authenticate.
     *
     * @param string $endpoint   Request endpoint.
     * @param string $method     Request method.
     * @param array  $parameters Request parameters.
     *
     * @return array
     */
    protected function authenticate($endpoint, $method, $parameters = [])
    {
        // Build URL.
        $url = $this->url . $endpoint;

        // Setup authentication.
        if ($this->isSsl) {
            // Set query string for authentication.
            if ($this->queryStringAuth) {
                $parameters['consumer_key']    = $this->consumerKey;
                $parameters['consumer_secret'] = $this->consumerSecret;
            } else {
                \curl_setopt($this->ch, CURLOPT_USERPWD, $this->consumerKey . ':' . $this->consumerSecret);
            }
        } else {
            $oAuth      = new OAuth($url, $this->consumerKey, $this->consumerSecret, $this->version, $method, $parameters);
            $parameters = $oAuth->getParameters();
        }

        // Include URL parameters.
        if (!empty($parameters)) {
            $url .= '?' . \http_build_query($parameters);
        }

        return [
            'url'        => $url,
            'parameters' => $parameters,
        ];
    }

    /**
     * Create request.
     *
     * @param string $endpoint   Request endpoint.
     * @param string $method     Request method.
     * @param array  $data       Request data.
     * @param array  $parameters Request parameters.
     *
     * @return Request
     */
    protected function createRequest($endpoint, $method, $data = [], $parameters = [])
    {
        $auth       = $this->authenticate($endpoint, $method, $parameters = []);
        $url        = $auth['url'];
        $parameters = $auth['parameters'];

        // Setup method.
        switch ($method) {
            case 'POST':
                $body = \json_encode($data);
                \curl_setopt($this->ch, CURLOPT_POST, true);
                \curl_setopt($this->ch, CURLOPT_POSTFIELDS, $body);
                break;
            case 'PUT':
                $body = \json_encode($data);
                \curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                \curl_setopt($this->ch, CURLOPT_POSTFIELDS, $body);
                break;
            case 'DELETE':
                $body = '';
                \curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;

            default:
                $body = '';
                break;
        }

        // Setup headers.
        $headers = [
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
            'User-Agent'   => 'WooCommerce API Client-PHP/' . Client::VERSION,
        ];

        $this->request = new Request($url, $method, $parameters, $headers, $body);

        return $this->getRequest();
    }

    /**
     * Get response headers.
     *
     * @return array
     */
    public function getResponseHeaders()
    {
        $headers = [];
        $lines   = \explode("\n", $this->responseHeaders);
        $lines   = \array_filter($lines, 'trim');

        foreach ($lines as $index => $line) {
            // Remove HTTP/xxx param.
            if (0 === $index) {
                continue;
            }

            list($key, $value) = \explode(': ', $line);
            $headers[$key] = $value;
        }

        return $headers;
    }

    /**
     * Create response.
     *
     * @return Response
     */
    protected function createResponse()
    {

        // Set response headers.
        $this->responseHeaders = '';
        \curl_setopt($this->ch, CURLOPT_HEADERFUNCTION, function($_, $headers) {
            $this->responseHeaders .= $headers;
            return \strlen($headers);
        });

        // Get response data.
        $body    = \curl_exec($this->ch);
        $code    = \curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
        $headers = $this->getResponseHeaders();

        // Register response.
        $this->response = new Response($code, $headers, $body);

        return $this->getResponse();
    }

    /**
     * Make requests.
     *
     * @param string $endpoint   Request endpoint.
     * @param string $method     Request method.
     * @param array  $data       Request data.
     * @param array  $parameters Request parameters.
     *
     * @return array
     */
    public function request($endpoint, $method, $data = [], $parameters = [])
    {
        // Initialize cURL.
        $this->ch = \curl_init();

        // Set request args.
        $request = $this->createRequest($endpoint, $method, $data, $parameters);

        // Default cURL settings.
        \curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, $this->verifySsl);
        \curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, $this->verifySsl);
        \curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
        \curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->timeout);
        \curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        \curl_setopt($this->ch, CURLOPT_HTTPHEADER, $request->getRawHeaders());
        \curl_setopt($this->ch, CURLOPT_URL, $request->getUrl());

        // Get response.
        $response = $this->createResponse();

        // Check for cURL errors.
        if (\curl_errno($this->ch)) {
            throw new HttpClientException('cURL Error: ' . \curl_error($this->ch), 0, $request, $response);
        }

        \curl_close($this->ch);

        $parsedResponse = $this->decodeResponseBody($response->getBody());

        $this->lookForErrors($parsedResponse, $request, $response);

        return $parsedResponse;
    }

    /**
     * Look for errors in the request.
     *
     * @param array    $parsedResponse Parsed body response.
     * @param Request  $request        Request data.
     * @param Response $response       Response data.
     */
    protected function lookForErrors($parsedResponse, $request, $response) {
        // Test if return a valid JSON.
        if (null === $parsedResponse) {
            throw new HttpClientException('Invalid JSON returned', $response->getCode(), $request, $response);
        }

        // Any non-200/201/202 response code indicates an error.
        if (!\in_array($response->getCode(), ['200', '201', '202'])) {
            if (!empty($parsedResponse['errors'][0])) {
                $errorMessage = $parsedResponse['errors'][0]['message'];
                $errorCode    = $parsedResponse['errors'][0]['code'];
            } else {
                $errorMessage = $parsedResponse['errors']['message'];
                $errorCode    = $parsedResponse['errors']['code'];
            }

            throw new HttpClientException(\sprintf('Error: %s [%s]', $errorMessage, $errorCode), $response->getCode(), $request, $response);
        }
    }

    /**
     * Decode response body.
     *
     * @param  string $data Response in JSON format.
     *
     * @return array
     */
    protected function decodeResponseBody($data)
    {
        // Remove any HTML or text from cache plugins or PHP notices.
        \preg_match('/\{(?:[^{}]|(?R))*\}/', $data, $matches);
        $data = isset($matches[0]) ? $matches[0] : '';

        return \json_decode($data, true);
    }

    /**
     * Get request data.
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Get response data.
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}

<?php

/**
 * WooCommerce REST API HTTP Client
 *
 * @category HttpClient
 * @package  Automattic/WooCommerce
 */

namespace Automattic\WooCommerce\HttpClient;

use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\BasicAuth;
use Automattic\WooCommerce\HttpClient\HttpClientException;
use Automattic\WooCommerce\HttpClient\OAuth;
use Automattic\WooCommerce\HttpClient\Options;
use Automattic\WooCommerce\HttpClient\Request;
use Automattic\WooCommerce\HttpClient\Response;

/**
 * REST API HTTP Client class.
 *
 * @package Automattic/WooCommerce
 */
class HttpClient
{
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
     * Client options.
     *
     * @var Options
     */
    protected $options;

    /**
     * The custom cURL options to use in the requests.
     *
     * @var array
     */
    private $customCurlOptions = [];

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

        $this->options        = new Options($options);
        $this->url            = $this->buildApiUrl($url);
        $this->consumerKey    = $consumerKey;
        $this->consumerSecret = $consumerSecret;
    }

    /**
     * Check if is under SSL.
     *
     * @return bool
     */
    protected function isSsl()
    {
        return 'https://' === \substr($this->url, 0, 8);
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
        $api = $this->options->isWPAPI() ? $this->options->apiPrefix() : '/wc-api/';

        return \rtrim($url, '/') . $api . $this->options->getVersion() . '/';
    }

    /**
     * Build URL.
     *
     * @param string $url        URL.
     * @param array  $parameters Query string parameters.
     *
     * @return string
     */
    protected function buildUrlQuery($url, $parameters = [])
    {
        if (!empty($parameters)) {
            if (false !== strpos($url, '?')) {
                $url .= '&' . \http_build_query($parameters);
            } else {
                $url .= '?' . \http_build_query($parameters);
            }
        }

        return $url;
    }

    /**
     * Authenticate.
     *
     * @param string $url        Request URL.
     * @param string $method     Request method.
     * @param array  $parameters Request parameters.
     *
     * @return array
     */
    protected function authenticate($url, $method, $parameters = [])
    {
        // Setup authentication.
        if (!$this->options->isOAuthOnly() && $this->isSsl()) {
            $basicAuth = new BasicAuth(
                $this->ch,
                $this->consumerKey,
                $this->consumerSecret,
                $this->options->isQueryStringAuth(),
                $parameters
            );
            $parameters = $basicAuth->getParameters();
        } else {
            $oAuth = new OAuth(
                $url,
                $this->consumerKey,
                $this->consumerSecret,
                $this->options->getVersion(),
                $method,
                $parameters,
                $this->options->oauthTimestamp()
            );
            $parameters = $oAuth->getParameters();
        }

        return $parameters;
    }

    /**
     * Setup method.
     *
     * @param string $method Request method.
     */
    protected function setupMethod($method)
    {
        if ('POST' == $method) {
            \curl_setopt($this->ch, CURLOPT_POST, true);
        } elseif (\in_array($method, ['PUT', 'DELETE', 'OPTIONS'])) {
            \curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, $method);
        }
    }

    /**
     * Get request headers.
     *
     * @param  bool $sendData If request send data or not.
     *
     * @return array
     */
    protected function getRequestHeaders($sendData = false)
    {
        $headers = [
            'Accept'     => 'application/json',
            'User-Agent' => $this->options->userAgent() . '/' . Client::VERSION,
        ];

        if ($sendData) {
            $headers['Content-Type'] = 'application/json;charset=utf-8';
        }

        return $headers;
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
        $body    = '';
        $url     = $this->url . $endpoint;
        $hasData = !empty($data);
        $headers = $this->getRequestHeaders($hasData);

        // HTTP method override feature which masks PUT and DELETE HTTP methods as POST method with added
        // ?_method=PUT query parameter and/or X-HTTP-Method-Override HTTP header.
        if (!in_array($method, ['GET', 'POST'])) {
            $usePostMethod = false;
            if ($this->options->isMethodOverrideQuery()) {
                $parameters = array_merge(['_method' => $method], $parameters);
                $usePostMethod = true;
            }
            if ($this->options->isMethodOverrideHeader()) {
                $headers['X-HTTP-Method-Override'] = $method;
                $usePostMethod = true;
            }
            if ($usePostMethod) {
                $method = 'POST';
            }
        }

        // Setup authentication.
        $parameters = $this->authenticate($url, $method, $parameters);

        // Setup method.
        $this->setupMethod($method);

        // Include post fields.
        if ($hasData) {
            $body = \json_encode($data);
            \curl_setopt($this->ch, CURLOPT_POSTFIELDS, $body);
        }

        $this->request = new Request(
            $this->buildUrlQuery($url, $parameters),
            $method,
            $parameters,
            $headers,
            $body
        );

        return $this->getRequest();
    }

    /**
     * Get response headers.
     *
     * @return array
     */
    protected function getResponseHeaders()
    {
        $headers = [];
        $lines   = \explode("\n", $this->responseHeaders);
        $lines   = \array_filter($lines, 'trim');

        foreach ($lines as $index => $line) {
            // Remove HTTP/xxx params.
            if (strpos($line, ': ') === false) {
                continue;
            }

            list($key, $value) = \explode(': ', $line);

            $headers[$key] = isset($headers[$key]) ? $headers[$key] . ', ' . trim($value) : trim($value);
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
        \curl_setopt($this->ch, CURLOPT_HEADERFUNCTION, function ($_, $headers) {
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
     * Set default cURL settings.
     */
    protected function setDefaultCurlSettings()
    {
        $verifySsl       = $this->options->verifySsl();
        $timeout         = $this->options->getTimeout();
        $followRedirects = $this->options->getFollowRedirects();

        \curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, $verifySsl);
        if (!$verifySsl) {
            \curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, $verifySsl);
        }
        if ($followRedirects) {
            \curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
        }
        \curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        \curl_setopt($this->ch, CURLOPT_TIMEOUT, $timeout);
        \curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        \curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->request->getRawHeaders());
        \curl_setopt($this->ch, CURLOPT_URL, $this->request->getUrl());

        foreach ($this->customCurlOptions as $customCurlOptionKey => $customCurlOptionValue) {
            \curl_setopt($this->ch, $customCurlOptionKey, $customCurlOptionValue);
        }
    }

    /**
     * Look for errors in the request.
     *
     * @param array $parsedResponse Parsed body response.
     */
    protected function lookForErrors($parsedResponse)
    {
        // Any non-200/201/202 response code indicates an error.
        if (!\in_array($this->response->getCode(), ['200', '201', '202'])) {
            $errors = isset($parsedResponse->errors) ? $parsedResponse->errors : $parsedResponse;
            $errorMessage = '';
            $errorCode = '';

            if (is_array($errors)) {
                $errorMessage = $errors[0]->message;
                $errorCode    = $errors[0]->code;
            } elseif (isset($errors->message, $errors->code)) {
                $errorMessage = $errors->message;
                $errorCode    = $errors->code;
            }

            throw new HttpClientException(
                \sprintf('Error: %s [%s]', $errorMessage, $errorCode),
                $this->response->getCode(),
                $this->request,
                $this->response
            );
        }
    }

    /**
     * Process response.
     *
     * @return \stdClass
     */
    protected function processResponse()
    {
        $body = $this->response->getBody();

        // Look for UTF-8 BOM and remove.
        if (0 === strpos(bin2hex(substr($body, 0, 4)), 'efbbbf')) {
            $body = substr($body, 3);
        }

        // strip out instances of the Unicode NULL character (\u0000) from property names
        $body = str_replace('\u0000', '', $body);

        $parsedResponse = \json_decode($body);

        // Test if return a valid JSON.
        if (JSON_ERROR_NONE !== json_last_error()) {
            $message = function_exists('json_last_error_msg') ? json_last_error_msg() : 'Invalid JSON returned';
            throw new HttpClientException(
                sprintf('JSON ERROR: %s', $message),
                $this->response->getCode(),
                $this->request,
                $this->response
            );
        }

        $this->lookForErrors($parsedResponse);

        return $parsedResponse;
    }

    /**
     * Make requests.
     *
     * @param string $endpoint   Request endpoint.
     * @param string $method     Request method.
     * @param array  $data       Request data.
     * @param array  $parameters Request parameters.
     *
     * @return \stdClass
     */
    public function request($endpoint, $method, $data = [], $parameters = [])
    {
        // Initialize cURL.
        $this->ch = \curl_init();

        // Set request args.
        $request = $this->createRequest($endpoint, $method, $data, $parameters);

        // Default cURL settings.
        $this->setDefaultCurlSettings();

        // Get response.
        $response = $this->createResponse();

        // Check for cURL errors.
        if (\curl_errno($this->ch)) {
            throw new HttpClientException('cURL Error: ' . \curl_error($this->ch), 0, $request, $response);
        }

        \curl_close($this->ch);

        return $this->processResponse();
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

    /**
     * Set custom cURL options to use in requests.
     *
     * @param array $curlOptions
     */
    public function setCustomCurlOptions(array $curlOptions)
    {
        $this->customCurlOptions = $curlOptions;
    }
}

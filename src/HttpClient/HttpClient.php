<?php
/**
 * WooCommerce REST API HTTP Client
 *
 * @category HttpClient
 * @package  Automattic/WooCommerce
 */

namespace Automattic\WooCommerce\HttpClient;

use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\Request;
use Automattic\WooCommerce\HttpClient\Response;

/**
 * REST API HTTP class.
 *
 * @package Automattic/WooCommerce
 */
class HttpClient
{

    const VERSION = 'v3';

    public $lastRequest;
    public $lastResponse;

    protected $ch;

    protected $url;
    protected $consumerKey;
    protected $consumerSecret;
    protected $version;
    protected $isSsl;
    protected $verifySsl;
    protected $timeout;

    private $responseHeaders;

    public function __construct($url, $consumerKey, $consumerSecret, $options)
    {
        $this->version        = $this->getVersion($options);
        $this->isSsl          = $this->isSsl($url);
        $this->url            = $this->buildApiUrl($url);
        $this->consumerKey    = $consumerKey;
        $this->consumerSecret = $consumerSecret;
        $this->verifySsl      = $this->verifySsl($options);
        $this->timeout        = $this->getTimeout($options);
    }

    protected function getVersion($options)
    {
        return isset($options['version']) ? $options['version'] : self::VERSION;
    }

    protected function isSsl($url)
    {
        return 'https://' === \substr($url, 0, 8);
    }

    protected function buildApiUrl($url)
    {
        return \rtrim($url, '/') . '/wc-api/' . $this->version . '/';
    }

    protected function verifySsl($options)
    {
        return isset($options['verify_ssl']) ? (bool) $options['verify_ssl'] : true;
    }

    protected function getTimeout($options)
    {
        return isset($options['timeout']) ? (int) $options['timeout'] : 15;
    }

    protected function createRequest($endpoint, $method, $data = [], $parameters = [])
    {

        $url = $this->url . $endpoint;

        switch ($method) {
            case 'POST':
                $body = \json_encode($data);
                \curl_setopt($this->ch, CURLOPT_POST, true);
                \curl_setopt($this->ch, CURLOPT_POSTFIELDS, $request->getBody());
                break;
            case 'PUT':
                $body = \json_encode($data);
                \curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                \curl_setopt($this->ch, CURLOPT_POSTFIELDS, $request->getBody());
                break;
            case 'DELETE':
                $body = '';
                \curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                \curl_setopt($this->ch, CURLOPT_POSTFIELDS, $request->getBody());
                break;

            default:
                $body = '';
                break;
        }

        $headers = [
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
            'User-Agent'   => 'WooCommerce API Client-PHP/' . Client::VERSION,
        ];

        $this->lastRequest = new Request($url, $method, $parameters, $headers, $body);

        return $this->lastRequest;
    }

    public function getResponseHeaders()
    {
        $headers = [];
        $lines   = \explode("\n", $this->responseHeaders);
        $lines   = \array_filter($lines, 'trim');

        foreach ($lines as $index => $line) {
            if (0 === $index) {
                continue;
            }

            list($key, $value) = \explode(': ', $line);
            $headers[$key] = $value;
        }

        return $headers;
    }

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

        // Register last response.
        $this->lastResponse = new Response($code, $headers, $body);

        return $this->lastResponse;
    }

    public function request($endpoint, $method, $data = [], $parameters = [])
    {
        // Initialize cURL.
        $this->ch = \curl_init();

        // Set request args.
        $request = $this->createRequest($endpoint, $method, $data, $parameters);

        // Default cURL settings.
        \curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, $this->verifySsl);
        \curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
        \curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->timeout);
        \curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        \curl_setopt($this->ch, CURLOPT_HTTPHEADER, $request->getRawHeaders());
        \curl_setopt($this->ch, CURLOPT_URL, $request->getUrl());

        // Get response.
        $response = $this->createResponse();

        \curl_close($this->ch);

        return $this->decodeResponseBody($response->getBody());
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
}

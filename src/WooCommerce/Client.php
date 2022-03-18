<?php

/**
 * WooCommerce REST API Client
 *
 * @category Client
 * @package  Automattic/WooCommerce
 */

namespace Automattic\WooCommerce;

use Automattic\WooCommerce\HttpClient\HttpClient;

/**
 * REST API Client class.
 *
 * @package Automattic/WooCommerce
 */
class Client
{
    /**
     * WooCommerce REST API Client version.
     */
    public const VERSION = '3.1.0';

    /**
     * HttpClient instance.
     *
     * @var HttpClient
     */
    public $http;

    /**
     * Initialize client.
     *
     * @param string $url            Store URL.
     * @param string $consumerKey    Consumer key.
     * @param string $consumerSecret Consumer secret.
     * @param array  $options        Options (version, timeout, verify_ssl, oauth_only).
     */
    public function __construct($url, $consumerKey, $consumerSecret, $options = [])
    {
        $this->http = new HttpClient($url, $consumerKey, $consumerSecret, $options);
    }

    /**
     * POST method.
     *
     * @param string $endpoint API endpoint.
     * @param array  $data     Request data.
     *
     * @return \stdClass
     */
    public function post($endpoint, $data)
    {
        return $this->http->request($endpoint, 'POST', $data);
    }

    /**
     * PUT method.
     *
     * @param string $endpoint API endpoint.
     * @param array  $data     Request data.
     *
     * @return \stdClass
     */
    public function put($endpoint, $data)
    {
        return $this->http->request($endpoint, 'PUT', $data);
    }

    /**
     * GET method.
     *
     * @param string $endpoint   API endpoint.
     * @param array  $parameters Request parameters.
     *
     * @return \stdClass
     */
    public function get($endpoint, $parameters = [])
    {
        return $this->http->request($endpoint, 'GET', [], $parameters);
    }

    /**
     * DELETE method.
     *
     * @param string $endpoint   API endpoint.
     * @param array  $parameters Request parameters.
     *
     * @return \stdClass
     */
    public function delete($endpoint, $parameters = [])
    {
        return $this->http->request($endpoint, 'DELETE', [], $parameters);
    }

    /**
     * OPTIONS method.
     *
     * @param string $endpoint API endpoint.
     *
     * @return \stdClass
     */
    public function options($endpoint)
    {
        return $this->http->request($endpoint, 'OPTIONS', [], []);
    }
}

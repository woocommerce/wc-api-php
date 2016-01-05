<?php
/**
 * WooCommerce REST API Client
 *
 * @category Client
 * @package  Automattic/WooCommerce
 * @author   Claudio Sanches <claudio.sanches@automattic.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/woothemes/wc-api-php
 */

namespace Automattic\WooCommerce;

use Automattic\WooCommerce\HttpClient\HttpClient;

/**
 * REST API Client class.
 *
 * @category Client
 * @package  Automattic/WooCommerce
 * @author   Claudio Sanches <claudio.sanches@automattic.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/woothemes/wc-api-php
 */
class Client
{

    /**
     * WooCommerce REST API Client version.
     */
    const VERSION  = '1.0.0';

    /**
     * Http client instance.
     *
     * @var HttpClient
     */
    private $client;

    /**
     * Initialize client.
     *
     * @param string $url            Store URL.
     * @param string $consumerKey    Consumer key.
     * @param string $consumerSecret Consumer secret.
     * @param array  $options        Options (version, timeout, verify_ssl).
     */
    public function __construct($url, $consumerKey, $consumerSecret, $options = [])
    {
        $this->client = new HttpClient($url, $consumerKey, $consumerSecret, $options);
    }

    /**
     * POST method.
     *
     * @param string $endpoint API endpoint.
     * @param array  $data     Request data.
     *
     * @return array
     */
    public function post($endpoint, $data)
    {
        return $this->client->request($endpoint, 'POST', $data);
    }

    /**
     * PUT method.
     *
     * @param string $endpoint API endpoint.
     * @param array  $data     Request data.
     *
     * @return array
     */
    public function put($endpoint, $data)
    {
        return $this->client->request($endpoint, 'PUT', $data);
    }

    /**
     * GET method.
     *
     * @param string $endpoint   API endpoint.
     * @param array  $parameters Request parameters.
     *
     * @return array
     */
    public function get($endpoint, $parameters = [])
    {
        return $this->client->request($endpoint, 'GET', [], $parameters);
    }

    /**
     * DELETE method.
     *
     * @param string $endpoint   API endpoint.
     * @param array  $parameters Request parameters.
     *
     * @return array
     */
    public function delete($endpoint, $parameters = [])
    {
        return $this->client->request($endpoint, 'DELETE', [], $parameters);
    }
}

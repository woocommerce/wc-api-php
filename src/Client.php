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
     * Instance of Automattic\WooCommerce\HttpClient\HttpClient.
     *
     * @var HttpClient
     */
    private $_http;

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
        $this->_http = new HttpClient($url, $consumerKey, $consumerSecret, $options);
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
        return $this->_http->request($endpoint, 'POST', $data);
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
        return $this->_http->request($endpoint, 'PUT', $data);
    }

    /**
     * GET method.
     *
     * @param string $endpoint API endpoint.
     * @param array  $params   Request params.
     *
     * @return array
     */
    public function get($endpoint, $params = [])
    {
        return $this->_http->request($endpoint, 'GET', [], $params);
    }

    /**
     * DELETE method.
     *
     * @param string $endpoint API endpoint.
     * @param array  $params   Request params.
     *
     * @return array
     */
    public function delete($endpoint, $params = [])
    {
        return $this->_http->request($endpoint, 'DELETE', [], $params);
    }
}

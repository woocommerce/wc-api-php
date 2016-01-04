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

use Automattic\WooCommerce\Request\Request;

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
     * Instance of Automattic\WooCommerce\Request\Request.
     *
     * @var Request
     */
    private $_request;

    /**
     * Initialize client.
     *
     * @param string $url            Store URL.
     * @param string $consumerKey    Consumer key.
     * @param string $consumerSecret Consumer secret.
     * @param array  $options        Options (version, timeout, verify_ssl).
     */
    public function __construct($url, $consumerKey, $consumerSecret, $options)
    {
        $this->_request = new Request($url, $consumerKey, $consumerSecret, $options);
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

    }

    /**
     * GET method.
     *
     * @param string $endpoint API endpoint.
     * @param array  $params   Request params.
     *
     * @return array
     */
    public function get($endpoint, $params)
    {

    }

    /**
     * DELETE method.
     *
     * @param string $endpoint API endpoint.
     * @param array  $params   Request params.
     *
     * @return array
     */
    public function delete($endpoint, $params)
    {

    }
}

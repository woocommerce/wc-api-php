<?php
/**
 * WooCommerce REST API HTTP Client Request
 *
 * @category HttpClient
 * @package  Automattic/WooCommerce
 * @author   Claudio Sanches <claudio.sanches@automattic.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/woothemes/wc-api-php
 */

namespace Automattic\WooCommerce\HttpClient;

/**
 * REST API HTTP Client Request class.
 *
 * @category HttpClient
 * @package  Automattic/WooCommerce
 * @author   Claudio Sanches <claudio.sanches@automattic.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/woothemes/wc-api-php
 */
class Request
{

    /**
     * Request endpoint.
     *
     * @var string
     */
    private $endpoint;

    /**
     * Request method.
     *
     * @var string
     */
    private $method;

    /**
     * Request paramenters.
     *
     * @var array
     */
    private $parameters;

    /**
     * Request headers.
     *
     * @var array
     */
    private $headers;

    /**
     * Request body.
     *
     * @var array
     */
    private $body;

    /**
     * Initialize request.
     *
     * @param string $endpoint   Request endpoint.
     * @param string $method     Request method.
     * @param array  $parameters Request paramenters.
     * @param array  $headers    Request headers.
     * @param array  $body       Request body.
     */
    public function __construct(
        $endpoint = '',
        $method = 'POST',
        $parameters = [],
        $headers = [],
        $body = []
    ) {
        $this->endpoint   = $endpoint;
        $this->method     = $method;
        $this->parameters = $parameters;
        $this->headers    = $headers;
        $this->body       = $body;
    }

    /**
     * Set endpoint.
     *
     * @param string $endpoint Request endpoint.
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
    }

    /**
     * Set method.
     *
     * @param string $method Request method.
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * Set parameters.
     *
     * @param array $parameters Request paramenters.
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Set headers.
     *
     * @param array $headers Request headers.
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    /**
     * Set body.
     *
     * @param array $body Request body.
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * Get endpoint.
     *
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * Get method.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Get parameters.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Get headers.
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Get body.
     *
     * @return array
     */
    public function getBody()
    {
        return $this->body;
    }
}

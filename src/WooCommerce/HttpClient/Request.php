<?php

/**
 * WooCommerce REST API HTTP Client Request
 *
 * @category HttpClient
 * @package  Automattic/WooCommerce
 */

namespace Automattic\WooCommerce\HttpClient;

/**
 * REST API HTTP Client Request class.
 *
 * @package Automattic/WooCommerce
 */
class Request
{
    /**
     * Request url.
     *
     * @var string
     */
    private $url;

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
     * @var string
     */
    private $body;

    /**
     * Initialize request.
     *
     * @param string $url        Request url.
     * @param string $method     Request method.
     * @param array  $parameters Request paramenters.
     * @param array  $headers    Request headers.
     * @param string $body       Request body.
     */
    public function __construct($url = '', $method = 'POST', $parameters = [], $headers = [], $body = '')
    {
        $this->url        = $url;
        $this->method     = $method;
        $this->parameters = $parameters;
        $this->headers    = $headers;
        $this->body       = $body;
    }

    /**
     * Set url.
     *
     * @param string $url Request url.
     */
    public function setUrl($url)
    {
        $this->url = $url;
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
     * @param string $body Request body.
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * Get url.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
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
     * Get raw headers.
     *
     * @return array
     */
    public function getRawHeaders()
    {
        $headers = [];

        foreach ($this->headers as $key => $value) {
            $headers[] = $key . ': ' . $value;
        }

        return $headers;
    }

    /**
     * Get body.
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }
}

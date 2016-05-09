<?php
/**
 * WooCommerce REST API HTTP Client Options
 *
 * @category HttpClient
 * @package  Automattic/WooCommerce
 */

namespace Automattic\WooCommerce\HttpClient;

/**
 * REST API HTTP Client Options class.
 *
 * @package Automattic/WooCommerce
 */
class Options
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
     * Options.
     *
     * @var array
     */
    private $options;

    /**
     * Initialize HTTP client options.
     *
     * @param array $options Client options.
     */
    public function __construct($options)
    {
        $this->options = $options;
    }

    /**
     * Get API version.
     *
     * @return string
     */
    public function getVersion()
    {
        return isset($this->options['version']) ? $this->options['version'] : self::VERSION;
    }

    /**
     * Check if need to verify SSL.
     *
     * @return bool
     */
    public function verifySsl()
    {
        return isset($this->options['verify_ssl']) ? (bool) $this->options['verify_ssl'] : true;
    }

    /**
     * Get timeout.
     *
     * @return int
     */
    public function getTimeout()
    {
        return isset($this->options['timeout']) ? (int) $this->options['timeout'] : self::TIMEOUT;
    }

    /**
     * Basic Authentication as query string.
     * Some old servers are not able to use CURLOPT_USERPWD.
     *
     * @return bool
     */
    public function isQueryStringAuth()
    {
        return isset($this->options['query_string_auth']) ? (bool) $this->options['query_string_auth'] : false;
    }

    /**
     * Check if is WP REST API.
     *
     * @return bool
     */
    public function isWPAPI()
    {
        return isset($this->options['wp_api']) ? (bool) $this->options['wp_api'] : false;
    }
}

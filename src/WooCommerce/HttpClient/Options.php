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
     * @param array $options Client options.
     *
     * @return string
     */
    public function getVersion($options)
    {
        return isset($options['version']) ? $options['version'] : self::VERSION;
    }

    /**
     * Check if need to verify SSL.
     *
     * @param array $options Client options.
     *
     * @return bool
     */
    public function verifySsl($options)
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
    public function getTimeout($options)
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
    public function isQueryStringAuth($options)
    {
        return isset($options['query_string_auth']) ? (bool) $options['query_string_auth'] : false;
    }
}

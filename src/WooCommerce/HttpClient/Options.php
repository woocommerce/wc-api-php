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
     *
     * @var string
     */
    public const VERSION = 'wc/v3';

    /**
     * Default request timeout.
     */
    public const TIMEOUT = 15;

    /**
     * Default WP API prefix.
     * Including leading and trailing slashes.
     */
    public const WP_API_PREFIX = '/wp-json/';

    /**
     * Default User Agent.
     * No version number.
     */
    public const USER_AGENT = 'WooCommerce API Client-PHP';

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
     * Only use OAuth.
     *
     * @return bool
     */
    public function isOAuthOnly()
    {
        return isset($this->options['oauth_only']) ? (bool) $this->options['oauth_only'] : false;
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
        return isset($this->options['wp_api']) ? (bool) $this->options['wp_api'] : true;
    }

    /**
     * Custom API Prefix for WP API.
     *
     * @return string
     */
    public function apiPrefix()
    {
        return isset($this->options['wp_api_prefix']) ? $this->options['wp_api_prefix'] : self::WP_API_PREFIX;
    }

    /**
     * oAuth timestamp.
     *
     * @return string
     */
    public function oauthTimestamp()
    {
        return isset($this->options['oauth_timestamp']) ? $this->options['oauth_timestamp'] : \time();
    }

    /**
     * Custom user agent.
     *
     * @return string
     */
    public function userAgent()
    {
        return isset($this->options['user_agent']) ? $this->options['user_agent'] : self::USER_AGENT;
    }

    /**
     * Get follow redirects.
     *
     * @return bool
     */
    public function getFollowRedirects()
    {
        return isset($this->options['follow_redirects']) ? (bool) $this->options['follow_redirects'] : false;
    }

    /**
     * Check is it needed to mask all non-GET/POST methods (PUT/DELETE/etc.) by using POST method with added
     * query parameter ?_method=METHOD into URL.
     *
     * @return bool
     */
    public function isMethodOverrideQuery()
    {
        return isset($this->options['method_override_query']) && $this->options['method_override_query'];
    }

    /**
     * Check is it needed to mask all non-GET/POST methods (PUT/DELETE/etc.) by using POST method with added
     * "X-HTTP-Method-Override: METHOD" HTTP header into request.
     *
     * @return bool
     */
    public function isMethodOverrideHeader()
    {
        return isset($this->options['method_override_header']) && $this->options['method_override_header'];
    }
}

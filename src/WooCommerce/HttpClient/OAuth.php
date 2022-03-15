<?php

/**
 * WooCommerce oAuth1.0
 *
 * @category HttpClient
 * @package  Automattic/WooCommerce
 */

namespace Automattic\WooCommerce\HttpClient;

/**
 * oAuth1.0 class.
 *
 * @package Automattic/WooCommerce
 */
class OAuth
{
    /**
     * OAuth signature method algorithm.
     */
    public const HASH_ALGORITHM = 'SHA256';

    /**
     * API endpoint URL.
     *
     * @var string
     */
    protected $url;

    /**
     * Consumer key.
     *
     * @var string
     */
    protected $consumerKey;

    /**
     * Consumer secret.
     *
     * @var string
     */
    protected $consumerSecret;

    /**
     * API version.
     *
     * @var string
     */
    protected $apiVersion;

    /**
     * Request method.
     *
     * @var string
     */
    protected $method;

    /**
     * Request parameters.
     *
     * @var array
     */
    protected $parameters;

    /**
     * Timestamp.
     *
     * @var string
     */
    protected $timestamp;

    /**
     * Initialize oAuth class.
     *
     * @param string $url            Store URL.
     * @param string $consumerKey    Consumer key.
     * @param string $consumerSecret Consumer Secret.
     * @param string $method         Request method.
     * @param string $apiVersion     API version.
     * @param array  $parameters     Request parameters.
     * @param string $timestamp      Timestamp.
     */
    public function __construct(
        $url,
        $consumerKey,
        $consumerSecret,
        $apiVersion,
        $method,
        $parameters = [],
        $timestamp = ''
    ) {
        $this->url            = $url;
        $this->consumerKey    = $consumerKey;
        $this->consumerSecret = $consumerSecret;
        $this->apiVersion     = $apiVersion;
        $this->method         = $method;
        $this->parameters     = $parameters;
        $this->timestamp      = $timestamp;
    }

    /**
     * Encode according to RFC 3986.
     *
     * @param string|array $value Value to be normalized.
     *
     * @return string
     */
    protected function encode($value)
    {
        if (is_array($value)) {
            return array_map([$this, 'encode'], $value);
        } else {
            return str_replace(['+', '%7E'], [' ', '~'], rawurlencode($value));
        }
    }

    /**
     * Normalize parameters.
     *
     * @param array $parameters Parameters to normalize.
     *
     * @return array
     */
    protected function normalizeParameters($parameters)
    {
        $normalized = [];

        foreach ($parameters as $key => $value) {
            // Percent symbols (%) must be double-encoded.
            $key   = $this->encode($key);
            $value = $this->encode($value);

            $normalized[$key] = $value;
        }

        return $normalized;
    }

    /**
     * Process filters.
     *
     * @param array $parameters Request parameters.
     *
     * @return array
     */
    protected function processFilters($parameters)
    {
        if (isset($parameters['filter'])) {
            $filters = $parameters['filter'];
            unset($parameters['filter']);
            foreach ($filters as $filter => $value) {
                $parameters['filter[' . $filter . ']'] = $value;
            }
        }

        return $parameters;
    }

    /**
     * Get secret.
     *
     * @return string
     */
    protected function getSecret()
    {
        $secret = $this->consumerSecret;

        // Fix secret for v3 or later.
        if (!\in_array($this->apiVersion, ['v1', 'v2'])) {
            $secret .= '&';
        }

        return $secret;
    }

    /**
     * Generate oAuth1.0 signature.
     *
     * @param array $parameters Request parameters including oauth.
     *
     * @return string
     */
    protected function generateOauthSignature($parameters)
    {
        $baseRequestUri = \rawurlencode($this->url);

        // Extract filters.
        $parameters = $this->processFilters($parameters);

        // Normalize parameter key/values and sort them.
        $parameters = $this->normalizeParameters($parameters);
        $parameters = $this->getSortedParameters($parameters);

        // Set query string.
        $queryString  = \implode('%26', $this->joinWithEqualsSign($parameters)); // Join with ampersand.
        $stringToSign = $this->method . '&' . $baseRequestUri . '&' . $queryString;
        $secret       = $this->getSecret();

        return \base64_encode(\hash_hmac(self::HASH_ALGORITHM, $stringToSign, $secret, true));
    }

    /**
     * Creates an array of urlencoded strings out of each array key/value pairs.
     *
     * @param  array  $params      Array of parameters to convert.
     * @param  array  $queryParams Array to extend.
     * @param  string $key         Optional Array key to append
     * @return string              Array of urlencoded strings
     */
    protected function joinWithEqualsSign($params, $queryParams = [], $key = '')
    {
        foreach ($params as $paramKey => $paramValue) {
            if ($key) {
                $paramKey = $key . '%5B' . $paramKey . '%5D'; // Handle multi-dimensional array.
            }

            if (is_array($paramValue)) {
                $queryParams = $this->joinWithEqualsSign($paramValue, $queryParams, $paramKey);
            } else {
                $string = $paramKey . '=' . $paramValue; // Join with equals sign.
                $queryParams[] = $this->encode($string);
            }
        }

        return $queryParams;
    }

    /**
     * Sort parameters.
     *
     * @param array $parameters Parameters to sort in byte-order.
     *
     * @return array
     */
    protected function getSortedParameters($parameters)
    {
        \uksort($parameters, 'strcmp');

        foreach ($parameters as $key => $value) {
            if (\is_array($value)) {
                \uksort($parameters[$key], 'strcmp');
            }
        }

        return $parameters;
    }

    /**
     * Get oAuth1.0 parameters.
     *
     * @return string
     */
    public function getParameters()
    {
        $parameters = \array_merge($this->parameters, [
            'oauth_consumer_key'     => $this->consumerKey,
            'oauth_timestamp'        => $this->timestamp,
            'oauth_nonce'            => \sha1(\microtime()),
            'oauth_signature_method' => 'HMAC-' . self::HASH_ALGORITHM,
        ]);

        // The parameters above must be included in the signature generation.
        $parameters['oauth_signature'] = $this->generateOauthSignature($parameters);

        return $this->getSortedParameters($parameters);
    }
}

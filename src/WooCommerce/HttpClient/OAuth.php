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
    const HASH_ALGORITHM = 'SHA256';

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
     * @var array
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
     * Initialize oAuth class.
     *
     * @param string $url            Store URL.
     * @param string $consumerKey    Consumer key.
     * @param string $consumerSecret Consumer Secret.
     * @param string $method         Request method.
     * @param array  $parameters     Request parameters.
     */
    public function __construct($url, $consumerKey, $consumerSecret, $apiVersion, $method, $parameters = [])
    {
        $this->url            = $url;
        $this->consumerKey    = $consumerKey;
        $this->consumerSecret = $consumerSecret;
        $this->apiVersion     = $apiVersion;
        $this->method         = $method;
        $this->parameters     = $parameters;
    }

    /**
     * Normalize strings.
     *
     * @param string $string String to be normalized.
     *
     * @return string
     */
    protected function normalizeString($string)
    {
        return \str_replace('%', '%25', \rawurlencode(\rawurldecode($string)));
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
            $key   = $this->normalizeString($key);
            $value = $this->normalizeString($value);

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
        \uksort($parameters, 'strcmp');

        // Set query string.
        $query = [];
        foreach ($parameters as $key => $value) {
            $query[] = $key . '%3D' . $value; // Join with equals sign.
        }

        $queryString  = \implode('%26', $query); // Join with ampersand.
        $stringToSign = $this->method . '&' . $baseRequestUri . '&' . $queryString;
        $secret       = $this->getSecret();

        return \base64_encode(\hash_hmac(self::HASH_ALGORITHM, $stringToSign, $secret, true));
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
            'oauth_timestamp'        => \time(),
            'oauth_nonce'            => \sha1(\microtime()),
            'oauth_signature_method' => 'HMAC-' . self::HASH_ALGORITHM,
        ]);

        // The parameters above must be included in the signature generation.
        $parameters['oauth_signature'] = $this->generateOauthSignature($parameters);

        return $this->getSortedParameters($parameters);
    }
}

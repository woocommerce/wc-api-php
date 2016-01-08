<?php
/**
 * WooCommerce Basic Authentication
 *
 * @category HttpClient
 * @package  Automattic/WooCommerce
 */

namespace Automattic\WooCommerce\HttpClient;

/**
 * Basic Authentication class.
 *
 * @package Automattic/WooCommerce
 */
class BasicAuth
{
    /**
     * cURL handle.
     *
     * @var resource
     */
    protected $ch;

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
     * Do query string auth.
     *
     * @var bool
     */
    protected $doQueryString;

    /**
     * Request parameters.
     *
     * @var array
     */
    protected $parameters;

    /**
     * Initialize Basic Authentication class.
     *
     * @param resource $ch             cURL handle.
     * @param string   $consumerKey    Consumer key.
     * @param string   $consumerSecret Consumer Secret.
     * @param bool     $doQueryString  Do or not query string auth.
     * @param array    $parameters     Request parameters.
     */
    public function __construct($ch, $consumerKey, $consumerSecret, $doQueryString, $parameters = [])
    {
        $this->ch             = $ch;
        $this->consumerKey    = $consumerKey;
        $this->consumerSecret = $consumerSecret;
        $this->doQueryString  = $doQueryString;
        $this->parameters     = $parameters;

        $this->processAuth();
    }

    /**
     * Process auth.
     */
    protected function processAuth()
    {
        if ($this->doQueryString) {
            $this->parameters['consumer_key']    = $this->consumerKey;
            $this->parameters['consumer_secret'] = $this->consumerSecret;
        } else {
            \curl_setopt($this->ch, CURLOPT_USERPWD, $this->consumerKey . ':' . $this->consumerSecret);
        }
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
}

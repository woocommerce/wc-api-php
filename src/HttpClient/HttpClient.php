<?php
/**
 * WooCommerce REST API HTTP Client
 *
 * @category HttpClient
 * @package  Automattic/WooCommerce
 */

namespace Automattic\WooCommerce\HttpClient;

use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\Request;
use Automattic\WooCommerce\HttpClient\Response;

/**
 * REST API HTTP class.
 *
 * @package Automattic/WooCommerce
 */
class HttpClient
{

    const VERSION = 'v3';
    protected $url;
    protected $consumerKey;
    protected $consumerSecret;
    protected $version;
    protected $isSsl;
    protected $verifySsl;
    protected $timeout;

    public $request;

    public function __construct($url, $consumerKey, $consumerSecret, $options)
    {
        $this->version        = $this->getVersion($options);
        $this->isSsl          = $this->isSsl($url);
        $this->url            = $this->buildApiUrl($url);
        $this->consumerKey    = $consumerKey;
        $this->consumerSecret = $consumerSecret;
        $this->verifySsl      = $this->verifySsl($options);
        $this->timeout        = $this->getTimeout($options);
    }

    protected function getVersion($options)
    {
        return isset($options['version']) ? $options['version'] : self::VERSION;
    }

    protected function isSsl($url)
    {
        return 'https://' === \substr($url, 0, 8);
    }

    protected function buildApiUrl($url)
    {
        return \rtrim($url, '/') . '/wc-api/' + $this->version + '/';
    }

    protected function verifySsl($options)
    {
        return isset($options['verify_ssl']) ? (bool) $options['verify_ssl'] : true;
    }

    protected function getTimeout($options)
    {
        return isset($options['timeout']) ? (int) $options['timeout'] : 15;
    }

    public function request($endpoint, $method, $data = [], $parameters = [])
    {
        $ch       = \curl_init();
        $request  = new Request();
        $response = new Response();

        $request->setHeaders([
            'Accept: application/json',
            'Content-Type: application/json',
            'User-Agent: WooCommerce API Client-PHP/' . Client::VERSION,
        ]);

        \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->verifySsl);
        \curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
        \curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        \curl_setopt($ch, CURLOPT_HTTPHEADER, $request->getHeaders());

        $body = \curl_exec($ch);
        $response->setBody($body);

        \curl_close($ch);

        return [
            'request'  => $request,
            'response' => $response,
        ];
    }
}

<?php
/**
 * WooCommerce REST API Request
 *
 * @category Request
 * @package  Automattic/WooCommerce
 * @author   Claudio Sanches <claudio.sanches@automattic.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/woothemes/wc-api-php
 */

namespace Automattic\WooCommerce\Request;

/**
 * REST API Request class.
 *
 * @category Request
 * @package  Automattic/WooCommerce
 * @author   Claudio Sanches <claudio.sanches@automattic.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/woothemes/wc-api-php
 */
class Request
{

    const VERSION = 'v3';
    protected $url;
    protected $consumerKey;
    protected $consumerSecret;
    protected $version;
    protected $isSsl;
    protected $verifySsl;
    protected $timeout;

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

    public function request($endpoint, $method, $data = [], $params = [])
    {
        $ch       = \curl_init();
        $request  = [];
        $response = [];

        $request['headers'] = [
            'Accept: application/json',
            'Content-Type: application/json',
            'User-Agent: WooCommerce API Client-PHP/1.0',
        ];

        \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->verifySsl);
        \curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->verifySsl);
        \curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
        \curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        \curl_setopt($ch, CURLOPT_HTTPHEADER, $request['headers']);

        $response['body'] = \curl_exec($ch);

        \curl_close($ch);

        return [
            'request'  => $request,
            'response' => $response,
        ];
    }
}

<?php
namespace Automattic\WooCommerce\Request;

class Request {

    protected $url;
    protected $consumerKey;
    protected $consumerSecret;
    protected $version;
    protected $isSsl;
    protected $args;

    public function __construct($url, $consumerKey, $consumerSecret, $args) {
        $this->version = $this->getVersion($args);
        $this->isSsl = $this->isSsl($url);
        $this->url = $this->buildApiUrl($url);
        $this->consumerKey = $consumerKey;
        $this->consumerSecret = $consumerSecret;
    }

    protected function getVersion($args) {
        return isset( $args['version'] ) ? $args['version'] : 'v3';
    }

    protected function isSsl($url) {
        return 'https://' === substr($url, 0, 8);
    }

    protected function buildApiUrl($url) {
        $url = '/' === substr($url, -1) ? $url : $url . '/';

        return $url . 'wc-api/' + $this->version + '/';
    }
}

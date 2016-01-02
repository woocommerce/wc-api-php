<?php
namespace Automattic\WooCommerce;

use Automattic\WooCommerce\Request\Request;

class Client {

    protected $url;
    protected $consumerKey;
    protected $consumerSecret;
    protected $args;
    private $request;

    public function __construct($url, $consumerKey, $consumerSecret, $args) {
        $this->request = new Request($url, $consumerKey, $consumerSecret, $args);
    }

    public function post($endpoint, $data) {

    }

    public function put($endpoint, $data) {

    }

    public function get($endpoint, $params) {

    }

    public function delete($endpoint, $params) {

    }
}

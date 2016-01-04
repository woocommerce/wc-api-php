<?php
namespace Automattic\WooCommerce;

use Automattic\WooCommerce\Request\Request;

class Client {

    private $request;

    public function __construct($url, $consumerKey, $consumerSecret, $options) {
        $this->request = new Request($url, $consumerKey, $consumerSecret, $options);
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

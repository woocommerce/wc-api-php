<?php

namespace Automattic\WooCommerce\Tests;

use \PHPUnit\Framework\TestCase as TestCase;

class ResponseTest extends TestCase
{

    protected $response;

    public function setUp()
    {
        $this->response = new \Automattic\WooCommerce\HttpClient\Response();
    }

    public function testCode()
    {
        $code = 200;
        $this->response->setCode($code);

        $this->assertEquals($code, $this->response->getCode());
    }

    public function testHeaders()
    {
        $headers = ['Content-Type' => 'application/json'];
        $this->response->setHeaders($headers);

        $this->assertEquals($headers, $this->response->getHeaders());
    }

    public function testBody()
    {
        $body = '{"product": {"id": 1}}';
        $this->response->setBody($body);

        $this->assertEquals($body, $this->response->getBody());
    }
}

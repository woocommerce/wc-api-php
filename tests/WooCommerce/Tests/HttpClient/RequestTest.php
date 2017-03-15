<?php

namespace Automattic\WooCommerce\Tests;

use \PHPUnit\Framework\TestCase as TestCase;

class RequestTest extends TestCase
{

    protected $request;

    public function setUp()
    {
        $this->request = new \Automattic\WooCommerce\HttpClient\Request();
    }

    public function testUrl()
    {
        $url = 'http://example.com';
        $this->request->setUrl($url);

        $this->assertEquals($url, $this->request->getUrl());
    }

    public function testMethod()
    {
        $method = 'PUT';
        $this->request->setMethod($method);

        $this->assertEquals($method, $this->request->getMethod());
    }

    public function testParameters()
    {
        $parameters = ['page' => 2];
        $this->request->setParameters($parameters);

        $this->assertEquals($parameters, $this->request->getParameters());
    }

    public function testHeaders()
    {
        $headers = ['Content-Type' => 'application/json'];
        $this->request->setHeaders($headers);

        $this->assertEquals($headers, $this->request->getHeaders());
    }

    public function testRawHeaders()
    {
        $headers = ['Content-Type' => 'application/json'];
        $this->request->setHeaders($headers);
        $rawHeaders = ['Content-Type: application/json'];

        $this->assertEquals($rawHeaders, $this->request->getRawHeaders());
    }

    public function testBody()
    {
        $body = '{"product": {"id": 1}}';
        $this->request->setBody($body);

        $this->assertEquals($body, $this->request->getBody());
    }
}

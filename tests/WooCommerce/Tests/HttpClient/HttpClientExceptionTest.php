<?php

namespace Automattic\WooCommerce\Tests;

use \PHPUnit\Framework\TestCase as TestCase;

class HttpClientExceptionTest extends TestCase
{

    protected $exception;

    public function setUp()
    {
        $request  = new \Automattic\WooCommerce\HttpClient\Request();
        $response = new \Automattic\WooCommerce\HttpClient\Response();

        $this->exception = new \Automattic\WooCommerce\HttpClient\HttpClientException('Test', 200, $request, $response);
    }

    public function testInstanceOfException()
    {
        $this->assertInstanceOf('Exception', $this->exception);
    }

    public function testGetMessage()
    {
        $this->assertEquals('Test', $this->exception->getMessage());
    }

    public function testGetCode()
    {
        $this->assertEquals(200, $this->exception->getCode());
    }

    public function testGetRequest()
    {
        $this->assertInstanceOf('Automattic\\WooCommerce\\HttpClient\\Request', $this->exception->getRequest());
    }

    public function testGetResponse()
    {
        $this->assertInstanceOf('Automattic\\WooCommerce\\HttpClient\\Response', $this->exception->getResponse());
    }
}

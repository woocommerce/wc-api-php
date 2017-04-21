<?php

namespace Automattic\WooCommerce\Tests;

use \PHPUnit\Framework\TestCase as TestCase;

class OptionsTest extends TestCase
{

    protected $options;

    public function setUp()
    {
        $this->options = new \Automattic\WooCommerce\HttpClient\Options([]);
    }

    public function testDefaultValueOfGetVersion()
    {
        $this->assertEquals('v3', $this->options->getVersion());
    }

    public function testDefaultValueOfVerifySsl()
    {
        $this->assertTrue($this->options->verifySsl());
    }

    public function testDefaultValueOfGetTimeout()
    {
        $this->assertEquals(15, $this->options->getTimeout());
    }

    public function testDefaultValueOfIsQueryStringAuth()
    {
        $this->assertFalse($this->options->isQueryStringAuth());
    }

    public function testisWPAPI()
    {
        $this->assertFalse($this->options->isWPAPI());
    }

    public function testDefaultValueOfIsApiPrefix()
    {
        $this->assertEquals('/wp-json/', $this->options->apiPrefix());
    }

    public function testDefaultValueOfUserAgent()
    {
        $this->assertEquals('WooCommerce API Client-PHP', $this->options->userAgent());
    }
}

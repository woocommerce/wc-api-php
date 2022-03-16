<?php

namespace Automattic\WooCommerce\LegacyTests;

use PHPUnit\Framework\TestCase as TestCase;

class OptionsTest extends TestCase
{
    protected $options;

    public function setUp()
    {
        $this->options = new \Automattic\WooCommerce\HttpClient\Options([]);
    }

    public function testDefaultValueOfGetVersion()
    {
        $this->assertEquals('wc/v3', $this->options->getVersion());
    }

    public function testDefaultValueOfVerifySsl()
    {
        $this->assertTrue($this->options->verifySsl());
    }

    public function testDefaultValueOfIsOAuthOnly()
    {
        $this->assertFalse($this->options->isOAuthOnly());
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
        $this->assertTrue($this->options->isWPAPI());
    }

    public function testDefaultValueOfIsApiPrefix()
    {
        $this->assertEquals('/wp-json/', $this->options->apiPrefix());
    }

    public function testDefaultValueOfUserAgent()
    {
        $this->assertEquals('WooCommerce API Client-PHP', $this->options->userAgent());
    }

    public function testDefaultValueOfgetFollowRedirects()
    {
        $this->assertFalse($this->options->getFollowRedirects());
    }

    public function testDefaultValueOfisMethodOverrideQuery()
    {
        $this->assertFalse($this->options->isMethodOverrideQuery());
    }

    public function testDefaultValueOfisMethodOverrideHeader()
    {
        $this->assertFalse($this->options->isMethodOverrideHeader());
    }
}

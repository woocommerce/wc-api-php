<?php

namespace Automattic\WooCommerce\Tests;

use \PHPUnit\Framework\TestCase as TestCase;

class BasicAuthTest extends TestCase
{

    protected $basicAuth;

    public function setUp()
    {
        $this->basicAuth = new \Automattic\WooCommerce\HttpClient\BasicAuth(null, 'ck_xxx', 'cs_xxx', true);
    }

    public function testGetParameters()
    {
        $default = [
            'consumer_key'    => 'ck_xxx',
            'consumer_secret' => 'cs_xxx',
        ];

        $this->assertEquals($default, $this->basicAuth->getParameters());
    }
}

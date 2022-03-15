<?php

namespace Automattic\WooCommerce\Tests;

use \PHPUnit\Framework\TestCase as TestCase;

class ClientTest extends TestCase
{

    public function testHttpInstanceOfHttpClient()
    {
        $client = new \Automattic\WooCommerce\Client('', '', '');

        $this->assertInstanceOf('Automattic\\WooCommerce\\HttpClient\\HttpClient', $client->http);
    }
}

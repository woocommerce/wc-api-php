<?php

namespace Automattic\WooCommerce\Tests;

use \PHPUnit\Framework\TestCase as TestCase;

class ClientTest extends TestCase {

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject $responseMock
     */
    protected $httpMock;

    public function testHttpInstanceOfHttpClient() {
        $client = new \Automattic\WooCommerce\Client('', '', '');

        $this->assertInstanceOf('Automattic\\WooCommerce\\HttpClient\\HttpClient', $client->http);

        return $client;
    }

    /**
     * @depends testHttpInstanceOfHttpClient
     */
    public function testGetWithMiddleware($client) {

        $response = json_decode('[ { "id": 21},{ "id": 22}]');

        $this->httpMock->expects($this->once())
            ->method('request')
            ->willReturn($response);

        $client->setHttp($this->httpMock);

        $filterById = function($responseData) {
            $filteredData = array();
            foreach ($responseData as $response) {

                if ($response->id <= 21) {

                    continue;
                }

                $filteredData[] = $response;
            }

            return $filteredData;

        };
        $res        = $client->get("order", [], $filterById);

        $expected = json_decode('[{"id":22}]');
        $this->assertEquals($expected, $res);
    }


    public function setUp() {

        $this->httpMock = $this->getMockBuilder('Automattic\\WooCommerce\\HttpClient\\HttpClient')
            ->disableOriginalConstructor()
            ->getMock();

    }

}

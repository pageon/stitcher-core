<?php

namespace Pageon\Integration;

use Stitcher\Test\StitcherTest;

class ServerTest extends StitcherTest
{
    protected function setUp()
    {
        parent::setUp();

        $this->startServer();
    }

    protected function tearDown()
    {
        $this->stopServer();

        parent::tearDown();
    }

    /** @test */
    public function get_index()
    {
        $response = $this->get('/');

        $this->assertEquals(200, $response->getStatusCode());
    }
}

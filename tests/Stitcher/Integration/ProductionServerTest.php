<?php

namespace Stitcher\Test\Integration;

use Stitcher\Test\CreateStitcherFiles;
use Stitcher\Test\CreateStitcherObjects;
use Stitcher\Test\StitcherTest;

class ProductionServerTest extends StitcherTest
{
    use CreateStitcherFiles;
    use CreateStitcherObjects;

    /** @test */
    public function get_index()
    {
        $response = $this->getProductionPage('/');

        $this->assertEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function it_serves_static_html_pages_on_the_production_server()
    {
        $this->parseAll();

        $body = (string) $this->getProductionPage('/')->getBody();
        $this->assertContains('<html>', $body);

        $body = (string) $this->getProductionPage('/entries/a')->getBody();
        $this->assertContains('<html>', $body);
    }

    /** @test */
    public function it_serves_dynamic_pages_on_the_production_server()
    {
        $this->parseAll();

        $body = (string) $this->getProductionPage('/test/1/abc')->getBody();

        $this->assertContains('test 1 abc', $body);
    }
}

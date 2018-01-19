<?php

namespace Stitcher\Test\Integration;

use Stitcher\Test\CreateStitcherFiles;
use Stitcher\Test\CreateStitcherObjects;
use Stitcher\Test\StitcherTest;

class DevelopmentServerTest extends StitcherTest
{
    use CreateStitcherFiles;
    use CreateStitcherObjects;

    /** @test */
    public function get_index()
    {
        $response = $this->getDevelopmentPage('/');

        $this->assertEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function it_serves_static_html_pages_on_the_development_server()
    {
        $this->parseAll();

        $body = (string) $this->getDevelopmentPage('/')->getBody();
        $this->assertContains('<html>', $body);

        $body = (string) $this->getDevelopmentPage('/entries/a')->getBody();
        $this->assertContains('<html>', $body);
    }

    /** @test */
    public function it_serves_dynamic_pages_on_the_development_server()
    {
        $this->parseAll();

        $body = (string) $this->getDevelopmentPage('/test/1/abc')->getBody();

        $this->assertContains('test 1 abc', $body);
    }
}

<?php

namespace Stitcher\Test\Integration;

use Stitcher\Test\CreateStitcherFiles;
use Stitcher\Test\CreateStitcherObjects;
use Stitcher\Test\StitcherTest;

class ServerTest extends StitcherTest
{
    use CreateStitcherFiles;
    use CreateStitcherObjects;

    /** @test */
    public function get_index()
    {
        $response = $this->get('/');

        $this->assertEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function it_serves_static_html_pages()
    {
        $this->parseAll();

        $body = (string) $this->get('/')->getBody();
        $this->assertContains('<html>', $body);

        $body = (string) $this->get('/entries/a')->getBody();
        $this->assertContains('<html>', $body);
    }
}

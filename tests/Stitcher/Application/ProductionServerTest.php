<?php

namespace Stitcher\Application;

use Stitcher\Test\CreateStitcherFiles;
use Stitcher\Test\CreateStitcherObjects;
use Stitcher\Test\StitcherTest;

class ProductionServerTest extends StitcherTest
{
    use CreateStitcherFiles;
    use CreateStitcherObjects;

    /** @test */
    public function it_serves_static_html()
    {
        $this->parseAll();

        $server = ProductionServer::make(__DIR__ .'/../../../data/public', '/entries');

        $html = $server->run();

        $this->assertContains('<html>', $html);
    }

    /** @test */
    public function it_serves_static_html_from_index()
    {
        $this->parseAll();

        $server = ProductionServer::make(__DIR__ .'/../../../data/public', '/');

        $html = $server->run();

        $this->assertContains('<html>', $html);
    }
}

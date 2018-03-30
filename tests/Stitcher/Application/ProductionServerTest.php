<?php

namespace Stitcher\Application;

use Stitcher\File;
use Stitcher\Test\CreateStitcherFiles;
use Stitcher\Test\CreateStitcherObjects;
use Stitcher\Test\StitcherTest;

class ProductionServerTest extends StitcherTest
{
    use CreateStitcherFiles;
    use CreateStitcherObjects;

    /** @test */
    public function it_serves_static_html(): void
    {
        $this->parseAll();

        $server = ProductionServer::make(File::path('public'));

        $html = $server->run();

        $this->assertContains('<html>', $html);
    }

    /** @test */
    public function it_serves_static_html_from_index(): void
    {
        $this->parseAll();

        $server = ProductionServer::make(File::path('public'));

        $html = $server->run();

        $this->assertContains('<html>', $html);
    }
}

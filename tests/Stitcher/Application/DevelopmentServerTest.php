<?php

namespace Stitcher\Application;

use Stitcher\Command\PartialParse;
use Stitcher\File;
use Stitcher\Test\CreateStitcherFiles;
use Stitcher\Test\CreateStitcherObjects;
use Stitcher\Test\StitcherTest;

class DevelopmentServerTest extends StitcherTest
{
    use CreateStitcherFiles;
    use CreateStitcherObjects;

    /** @var \Stitcher\Command\PartialParse */
    private $command;

    protected function setUp()
    {
        parent::setUp();

        $configurationFile = File::path('config/site.yaml');

        $this->createAllTemplates();
        $this->createSiteConfiguration($configurationFile);
        $this->createDataFile();
        $this->createImageFiles();

        $this->command = PartialParse::make(
            File::path('public'),
            $configurationFile,
            $this->createPageParser(),
            $this->createPageRenderer()
        );
    }

    /** @test */
    public function it_serves_static_html()
    {
        $server = DevelopmentServer::make(File::path('public'), $this->command, '/entries');

        $html = $server->run();

        $this->assertContains('<html>', $html);
    }

    /** @test */
    public function it_serves_static_html_from_index()
    {
        $server = DevelopmentServer::make(File::path('public'), $this->command, '/');

        $html = $server->run();

        $this->assertContains('<html>', $html);
    }
}

<?php

namespace Pageon\Integration;

use Stitcher\Command\Parse;
use Stitcher\File;
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
        $configurationFile = File::path('config/site.yaml');

        $this->createAllTemplates();
        $this->createSiteConfiguration($configurationFile);
        $this->createDataFile();
        $this->createImageFiles();

        $command = Parse::make(
            File::path('public'),
            $configurationFile,
            $this->createPageParser(),
            $this->createPageRenderer()
        );

        $command->execute();

        $body = (string) $this->get('/')->getBody();
        $this->assertContains('<html>', $body);

        $body = (string) $this->get('/entries/a')->getBody();
        $this->assertContains('<html>', $body);
    }
}

<?php

namespace Stitcher\Test\Stitcher\Command;

use Stitcher\Task\Parse;
use Stitcher\File;
use Stitcher\Task\RenderSiteMap;
use Stitcher\Test\CreateStitcherObjects;
use Stitcher\Test\CreateStitcherFiles;
use Stitcher\Test\StitcherTest;

class ParseTest extends StitcherTest
{
    use CreateStitcherObjects;
    use CreateStitcherFiles;

    /** @test */
    public function test_parse(): void
    {
        $this->createAllTemplates();
        $this->createConfigurationFile();

        $siteMap = $this->createSiteMap();

        $command = Parse::make(
            File::path('public'),
            File::path('site.yaml'),
            $this->createPageParser(),
            $this->createPageRenderer(),
            $siteMap
        );

        $command->addSubTask(new RenderSiteMap(File::path('public'), $siteMap));

        $command->execute();

        $this->assertFileExists(File::path('public/index.html'));
        $this->assertFileExists(File::path('public/test.html'));
        $this->assertFileExists(File::path('public/sitemap.xml'));
    }

    private function createConfigurationFile(): void
    {
        File::write('site.yaml', <<<EOT
/:
    template: index.twig
/test:
    template: index.twig
EOT
        );
    }
}

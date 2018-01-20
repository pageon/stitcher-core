<?php

namespace Stitcher\Task;

use Stitcher\File;
use Stitcher\Test\CreateStitcherFiles;
use Stitcher\Test\CreateStitcherObjects;
use Stitcher\Test\StitcherTest;

class PartialParseTest extends StitcherTest
{
    use CreateStitcherObjects;
    use CreateStitcherFiles;

    /** @var \Stitcher\Task\PartialParse */
    private $command;

    protected function setUp()
    {
        parent::setUp();

        $configurationFile = File::path('src/site.yaml');

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
    public function it_parses_only_one_page()
    {
        $this->command->setFilter('/entries');

        $this->command->execute();

        $this->assertFileExists(File::path('public/entries.html'));
        $this->assertFileNotExists(File::path('public/index.html'));
    }

    /** @test */
    public function it_parses_a_collection()
    {
        $this->command->setFilter('/entries/a');

        $this->command->execute();

        $this->assertFileExists(File::path('public/entries/a.html'));
    }

    /** @test */
    public function it_parses_a_paginated_page()
    {
        $this->command->setFilter('/entries/page-1');

        $this->command->execute();

        $this->assertFileExists(File::path('public/entries/page-1.html'));
    }
}

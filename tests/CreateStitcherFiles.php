<?php

namespace Stitcher\Test;

use Stitcher\Command\Parse;
use Stitcher\File;

trait CreateStitcherFiles
{
    protected function createIndexTemplate(): void
    {
    }

    protected function createAllTemplates(): void
    {
    }

    protected function createDataFile()
    {
    }

    protected function createImageFiles()
    {
    }

    protected function createSiteConfiguration(string $configurationPath = null): void
    {
    }

    protected function parseAll(): void
    {
        $configurationFile = File::path('src/site.yaml');

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
    }
}

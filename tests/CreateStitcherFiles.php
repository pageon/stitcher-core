<?php

namespace Stitcher\Test;

use Stitcher\File;
use Symfony\Component\Filesystem\Filesystem;

trait CreateStitcherFiles
{
    protected function createIndexTemplate(): void
    {
        $fs = new Filesystem();

        $fs->copy(__DIR__ . '/resources/twig/index.twig', File::path('template/index.twig'));
    }

    protected function createAllTemplates(): void
    {
        $fs = new Filesystem();

        $fs->copy(__DIR__ . '/resources/twig/_partials/main.twig', File::path('template/_partials/main.twig'));
        $fs->copy(__DIR__ . '/resources/twig/overview.twig', File::path('template/overview.twig'));
        $fs->copy(__DIR__ . '/resources/twig/detail.twig', File::path('template/detail.twig'));
        $this->createIndexTemplate();
    }

    protected function createDataFile()
    {
        $fs = new Filesystem();

        $fs->copy(__DIR__ . '/resources/data/entries.yaml', File::path('data/entries.yaml'));
    }

    protected function createImageFiles()
    {
        $fs = new Filesystem();

        $fs->copy(__DIR__ . '/resources/green_large.jpg', File::path('images/green_large.jpg'));
        $fs->copy(__DIR__ . '/resources/green.jpg', File::path('images/green.jpg'));
    }

    protected function createSiteConfiguration(string $configurationPath = null): void
    {
        $fs = new Filesystem();
        $configurationPath = $configurationPath ?? File::path('config/site.yaml');

        $fs->copy(__DIR__ . '/resources/config/site.yaml', $configurationPath);
    }
}

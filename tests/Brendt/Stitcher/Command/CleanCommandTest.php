<?php

namespace Brendt\Stitcher\Command;

use Brendt\Stitcher\App;
use Brendt\Test\CommandTestCase;
use Symfony\Component\Filesystem\Filesystem;

class CleanCommandTest extends CommandTestCase
{
    public function setUp()
    {
        App::init('./tests/config.yml');
    }

    public function test_clean_command_cleans_public_and_cache_folders()
    {
        $fs = new Filesystem();
        $publicPath = App::getParameter('directories.public') . '/index.html';
        $cachePath = App::getParameter('directories.cache') . '/cachefile';
        $fs->dumpFile($publicPath, 'abc');
        $fs->dumpFile($cachePath, 'abc');

        $this->assertTrue($fs->exists($publicPath));
        $this->assertTrue($fs->exists($cachePath));

        $this->runCommand('site:clean --force');

        $this->assertFalse($fs->exists($publicPath));
        $this->assertFalse($fs->exists($cachePath));
    }
}

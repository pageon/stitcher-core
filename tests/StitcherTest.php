<?php

namespace Stitcher\Test;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Stitcher\File;
use Symfony\Component\Filesystem\Filesystem;

abstract class StitcherTest extends TestCase
{
    protected function getTestDir()
    {
        return __DIR__;
    }

    protected function setUp()
    {
        parent::setUp();

        File::base(__DIR__ . '/../data');

        $fs = new Filesystem();

        $fs->copy(__DIR__ . '/resources/.env', File::path('.env'));
        $fs->mirror(__DIR__ . '/resources/config', File::path('config'));
    }

    protected function tearDown()
    {
        parent::tearDown();

        $fs = new Filesystem();
        $dataDir = File::path();

        if ($fs->exists($dataDir)) {
            $fs->remove($dataDir);
        }

        File::base(null);
    }

    protected function get(string $url): Response
    {
        $url = ltrim($url, '/');
        $client = new Client();
        $host = StitcherTestBootstrap::$host;

        return $client->request('GET', "{$host}/{$url}");
    }
}

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

        SetUp::run();
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

    protected function getProductionPage(string $url): Response
    {
        $url = ltrim($url, '/');

        $client = new Client();

        $host = StitcherTestBootstrap::$productionHost;

        return $client->request('GET', "{$host}/{$url}");
    }

    protected function getDevelopmentPage(string $url): Response
    {
        $url = ltrim($url, '/');

        $client = new Client();

        $host = StitcherTestBootstrap::$developmentHost;

        return $client->request('GET', "{$host}/{$url}");
    }
}

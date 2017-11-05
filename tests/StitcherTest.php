<?php

namespace Stitcher\Test;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Stitcher\File;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

abstract class StitcherTest extends TestCase
{
    /** @var Process */
    protected static $serverProcess = null;
    protected $host = 'localhost:8181';

    protected function getTestDir()
    {
        return __DIR__;
    }

    protected function setUp()
    {
        parent::setUp();

        File::base(__DIR__ . '/../data');
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

    public static function tearDownAfterClass()
    {
        if (!self::$serverProcess) {
            return;
        }

        self::$serverProcess->stop();
        self::$serverProcess = null;
    }

    protected function get(string $url): Response
    {
        $url = ltrim($url, '/');
        $client = new Client();

        return $client->request('GET', "{$this->host}/{$url}");
    }

    protected function startServer()
    {
        if (self::$serverProcess) {
            self::$serverProcess->stop();
        }

        $documentRoot = __DIR__.'/public';
        $router = "{$documentRoot}/index.php";

        self::$serverProcess = new Process("php -S {$this->host} {$router} >/dev/null 2>&1 & echo $!");
        self::$serverProcess->start();
    }

    protected function stopServer()
    {
        if (!self::$serverProcess) {
            return;
        }

        self::$serverProcess->stop();
        self::$serverProcess = null;
    }
}

<?php

namespace Stitcher\Test;

use Stitcher\File;
use Symfony\Component\Process\Process;

class StitcherTestBootstrap
{
    /** @var Process */
    protected static $serverProcess = null;
    public static $host = 'localhost:8181';

    public function __construct()
    {
        SetUp::run();

        register_shutdown_function(function () {
            $this->stopServer();
        });

        $this->startServer();
    }

    protected function startServer()
    {
        if (self::$serverProcess) {
            self::$serverProcess->stop();
        }

        $host = self::$host;
        $documentRoot = File::path('public');
        $router = "{$documentRoot}/index.php";

        self::$serverProcess = new Process("php -S {$host} {$router} >/dev/null 2>&1 & echo $!");
        self::$serverProcess->start();
    }

    protected function stopServer()
    {
        if (! self::$serverProcess) {
            return;
        }

        self::$serverProcess->stop();
        self::$serverProcess = null;
    }
}

<?php

namespace Stitcher\Test;

use Stitcher\File;
use Symfony\Component\Process\Process;

class StitcherTestBootstrap
{
    /** @var Process */
    protected static $productionServerProcess = null;

    /** @var Process */
    protected static $developmentServerProcess = null;

    public static $productionHost = 'localhost:8181';

    public static $developmentHost = 'localhost:8282';

    public function __construct()
    {
        SetUp::run();

        register_shutdown_function(function () {
            $this->stopServer();
        });

        $this->startServer();
    }

    protected function startServer(): void
    {
        $this->startProductionServer();
        $this->startDevelopmentServer();
    }

    protected function stopServer(): void
    {
        $this->stopProductionServer();
        $this->stopDevelopmentServer();
    }

    protected function startProductionServer(): void
    {
        if (self::$productionServerProcess) {
            self::$productionServerProcess->stop();
        }

        $host = self::$productionHost;
        $documentRoot = File::path('public');
        $router = "{$documentRoot}/index.php";

        self::$productionServerProcess = new Process("php -S {$host} {$router} >/dev/null 2>&1 & echo $!");
        self::$productionServerProcess->start();
    }

    protected function startDevelopmentServer(): void
    {
        if (self::$developmentServerProcess) {
            self::$developmentServerProcess->stop();
        }

        $host = self::$developmentHost;
        $documentRoot = File::path('public');
        $router = "{$documentRoot}/index.php";

        self::$developmentServerProcess = new Process("ENV=\"development\" php -S {$host} {$router} >/dev/null 2>&1 & echo $!");
        self::$developmentServerProcess->start();
    }

    protected function stopProductionServer(): void
    {
        if (! self::$productionServerProcess) {
            return;
        }

        self::$productionServerProcess->stop();
        self::$productionServerProcess = null;
    }

    protected function stopDevelopmentServer(): void
    {
        if (! self::$developmentServerProcess) {
            return;
        }

        self::$developmentServerProcess->stop();
        self::$developmentServerProcess = null;
    }
}

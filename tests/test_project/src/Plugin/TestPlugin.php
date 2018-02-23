<?php

namespace Stitcher\Test\Plugin;

use Stitcher\App;
use Stitcher\Plugin;

class TestPlugin implements Plugin
{
    public static $service;

    public static function getConfigurationPath(): ?string
    {
        return __DIR__ . '/../../config/testPlugin/config.php';
    }

    public static function getServicesPath(): ?string
    {
        return __DIR__ . '/../../config/testPlugin/services.yaml';
    }

    public static function boot(): void
    {
        self::$service = App::get(TestPluginService::class);

        return;
    }
}

<?php

namespace Stitcher\Test\Plugin;

use Stitcher\Plugin;

class TestPlugin implements Plugin
{
    public static function getConfigurationPath(): ?string
    {
        return __DIR__ . '/../../config/testPlugin/config.php';
    }

    public static function getServicesPath(): ?string
    {
        return __DIR__ . '/../../config/testPlugin/services.yaml';
    }
}

<?php

namespace Stitcher\Test\Plugin;

use Stitcher\Plugin;

class TestPlugin implements Plugin
{
    public static function getConfigurationPath(): ?string
    {
        return null;
    }

    public static function getServicesPath(): ?string
    {
        return null;
    }
}

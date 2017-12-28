<?php

namespace Stitcher\Exception;

use Exception;

class InvalidPlugin extends Exception
{
    public static function doesntImplementPluginInterface(string $class): InvalidPlugin
    {
        return new self("Plugin `{$class}` doesn't implement `Stitcher\Plugin`");
    }

    public static function configurationFileNotFound(string $class, string $configurationPath): InvalidPlugin
    {
        return new self("Configuration file for plugin `{$class}` not found. Searched for `{$configurationPath}`.");
    }

    public static function configurationMustBeArray(string $class, string $configurationPath): InvalidPlugin
    {
        return new self("Configuration file for plugin `{$class}` must return an array. Searched in `{$configurationPath}`.");
    }

    public static function serviceFileNotFound(string $class, string $servicePath): InvalidPlugin
    {
        return new self("Service file for plugin `{$class}` not found. Searched for `{$servicePath}`.");
    }
}

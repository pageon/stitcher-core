<?php

namespace Brendt\Stitcher\Exception;

class PluginException extends \Exception
{
    public static function pluginNotFound($className, $path)
    {
        return new self("Could not find the plugin named `{$className}` in this path: {$path}.");
    }

    public static function invalidPlugin($className)
    {
        return new self("Could not load the plugin named `{$className}` because it doesn't implement the `Brendt\\Stitcher\\Plugin\\Plugin` interface.");
    }
}

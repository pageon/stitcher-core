<?php

namespace Brendt\Stitcher\Exception;

class ConfigurationException extends StitcherException
{
    public static function requiredAdapterOptions($adapter, ...$fields) : ConfigurationException
    {
        $entries = count($fields) > 1 ? 'entries' : 'entry';
        $are = count($fields) > 1 ? 'are' : 'is';
        $fields = implode('`, `', $fields);

        return new self("The configuration {$entries} `{$fields}` {$are} required when using the {$adapter} adapter.");
    }

    public static function fileNotFound($path) : ConfigurationException
    {
        return new self("Could not load file: `{$path}`.");
    }

    public static function pluginNotFound($path) : ConfigurationException
    {
        return new self("Could not find plugin in {$path}. Looking for a `*Plugin.php` file in this directory");
    }
}

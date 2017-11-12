<?php

namespace Stitcher\Exception;

class InvalidConfiguration extends \Exception
{
    public static function pageIdAndTemplateRequired(): InvalidConfiguration
    {
        return new self('To create a page, both the `id` and `template` keys are required.');
    }

    public static function fileNotFound(string $path): InvalidConfiguration
    {
        return new self("File with path `{$path}` could not be found.");
    }

    public static function invalidAdapterConfiguration(string $adapter, string $fields): InvalidConfiguration
    {
        return new self("The {$adapter} adapter requires following configuration: {$fields}");
    }

    public static function dotEnvNotFound(string $directory): InvalidConfiguration
    {
        return new self("Could not find `.env` file. Looked in {$directory}");
    }

    public static function missingParameter(string $parameter): InvalidConfiguration
    {
        return new self("Missing parameter `{$parameter}`, did you add it in your config file?");
    }
}

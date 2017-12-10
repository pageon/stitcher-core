<?php

namespace Pageon;

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;
use Illuminate\Support\Arr;
use Iterator;
use Stitcher\Exception\InvalidConfiguration;
use Stitcher\File;
use Symfony\Component\Finder\Finder;

class Config
{
    protected static $env;
    protected static $loadedConfiguration = [];

    public static function init()
    {
        self::$env = new Dotenv(File::path());

        try {
            self::$env->load();
        } catch (InvalidPathException $e) {
            throw InvalidConfiguration::dotEnvNotFound(File::path());
        }

        $configurationFiles = Finder::create()->files()->in(File::path('config'))->name('*.php')->getIterator();

        self::loadDefaults();

        $loadedConfiguration = self::load($configurationFiles);

        self::$loadedConfiguration = array_merge(self::$loadedConfiguration, Arr::dot($loadedConfiguration));
    }

    public static function get(string $key)
    {
        return self::$loadedConfiguration[$key] ?? null;
    }

    public static function all(): array
    {
        return self::$loadedConfiguration;
    }

    protected static function loadDefaults(): void
    {
        self::$loadedConfiguration['rootDirectory'] = File::path();
        self::$loadedConfiguration['templateRenderer'] = 'twig';
    }

    protected static function load(Iterator $configurationFiles): array
    {
        $loadedConfiguration = [];

        foreach ($configurationFiles as $configurationFile) {
            $loadedFileConfiguration = require $configurationFile;

            if (!is_array($loadedFileConfiguration)) {
                continue;
            }

            $loadedConfiguration = array_merge($loadedConfiguration, $loadedFileConfiguration);
        }

        return $loadedConfiguration;
    }
}

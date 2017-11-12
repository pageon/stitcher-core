<?php

namespace Pageon;

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;
use Illuminate\Support\Arr;
use Stitcher\Exception\InvalidConfiguration;
use Stitcher\File;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class Config
{
    protected static $env;
    protected static $loadedConfig = [];

    public static function init()
    {
        self::$env = new Dotenv(File::path());

        try {
            self::$env->load();
        } catch (InvalidPathException $e) {
            throw InvalidConfiguration::dotEnvNotFound(File::path());
        }

        $configFiles = Finder::create()->files()->in(File::path('config'))->name('*.php');

        $unparsedConfig = [];

        /** @var SplFileInfo $configFile */
        foreach ($configFiles as $configFile) {
            $fileConfig = require $configFile;

            if (!is_array($fileConfig)) {
                continue;
            }

            $unparsedConfig = array_merge($unparsedConfig, $fileConfig);
        }

        self::$loadedConfig = Arr::dot($unparsedConfig);
    }

    public static function get(string $key)
    {
        return self::$loadedConfig[$key] ?? null;
    }

    public static function all(): array
    {
        return self::$loadedConfig;
    }
}

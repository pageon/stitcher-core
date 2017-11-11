<?php

namespace Pageon;

use Dotenv\Dotenv;
use Illuminate\Support\Arr;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class Config
{
    protected static $env;
    protected static $loadedConfig = [];

    public static function init(string $basePath)
    {
        $basePath = rtrim($basePath, '/');
        self::$env = new Dotenv($basePath);
        self::$env->load();

        $configFiles = Finder::create()->files()->in($basePath . '/config')->name('*.php');

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

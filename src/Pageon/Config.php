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
    protected static $plugins = [];

    public static function init(): void
    {
        self::$env = new Dotenv(File::path());

        try {
            self::$env->load();
        } catch (InvalidPathException $e) {
            throw InvalidConfiguration::dotEnvNotFound(File::path());
        }

        $loadedConfiguration = [];

        if (is_dir(File::path('config'))) {
            $configurationFiles = Finder::create()->files()->in(File::path('config'))->name('*.php')->getIterator();

            $loadedConfiguration = array_merge($loadedConfiguration, self::load($configurationFiles));
        }

        if (file_exists(File::path('src/config.php'))) {
            $sourceConfigurationFile = Finder::create()->files()->in(File::path('src'))->name('config.php')->getIterator();

            $loadedConfiguration = array_merge($loadedConfiguration, self::load($sourceConfigurationFile));
        }

        self::registerPlugins($loadedConfiguration);

        self::registerConfiguration($loadedConfiguration);
    }

    public static function get(string $key)
    {
        return self::$loadedConfiguration[$key] ?? null;
    }

    public static function all(): array
    {
        return self::$loadedConfiguration;
    }

    public static function plugins(): array
    {
        return self::$plugins;
    }

    protected static function defaults(): array
    {
        return [
            'rootDirectory' => File::path(),
            'resourcesPath' => File::path('resources'),
            'templateRenderer' => 'twig',
            'staticFiles' => [],
            'cacheStaticFiles' => false,
            'cacheImages' => true,
            'siteUrl' => '',
            'errorPages' => [],
        ];
    }

    protected static function load(Iterator $configurationFiles): array
    {
        $loadedConfiguration = [];

        foreach ($configurationFiles as $configurationFile) {
            $loadedFileConfiguration = require $configurationFile;

            if (! \is_array($loadedFileConfiguration)) {
                continue;
            }

            $loadedConfiguration = array_merge($loadedConfiguration, $loadedFileConfiguration);
        }

        return $loadedConfiguration;
    }

    protected static function registerPlugins(array $loadedConfiguration): void
    {
        self::$plugins = $loadedConfiguration['plugins'] ?? [];
    }

    protected static function registerConfiguration(array $loadedConfiguration): void
    {
        self::$loadedConfiguration = array_merge(
            self::defaults(),
            $loadedConfiguration,
            Arr::dot($loadedConfiguration)
        );
    }
}

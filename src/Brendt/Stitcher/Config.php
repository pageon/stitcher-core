<?php

namespace Brendt\Stitcher;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

/**
 * Config helper class
 *
 * @package Brendt\Stitcher
 */
class Config
{
    const ASYNC = 'async';
    const CACHE_CDN = 'cache.cdn';
    const CACHE_IMAGES = 'cache.images';
    const CDN = 'cdn';
    const DIRECTORIES_CACHE = 'directories.cache';
    const DIRECTORIES_PUBLIC = 'directories.public';
    const DIRECTORIES_SRC = 'directories.src';
    const ENGINES_IMAGE = 'engines.image';
    const ENGINES_OPTIMIZER = 'engines.optimizer';
    const ENGINES_TEMPLATE = 'engines.template';
    const ENGINES_MINIFIER = 'engines.minifier';
    const ENVIRONMENT = 'environment';
    const META = 'meta';
    const OPTIMIZER_OPTIONS = 'optimizer.options';
    const REDIRECT_HTTPS = 'redirect.https';
    const REDIRECT_WWW = 'redirect.www';
    const SITEMAP_URL = 'sitemap.url';

    public static function getDefaults() : array
    {
        return [
            self::ASYNC              => true,
            self::ENVIRONMENT        => 'development',
            self::DIRECTORIES_SRC    => './src',
            self::DIRECTORIES_PUBLIC => './public',
            self::DIRECTORIES_CACHE  => './.cache',
            self::META               => [],
            self::ENGINES_MINIFIER   => false,
            self::ENGINES_TEMPLATE   => 'smarty',
            self::ENGINES_IMAGE      => 'gd',
            self::ENGINES_OPTIMIZER  => true,
            self::CDN                => [],
            self::CACHE_IMAGES       => true,
            self::CACHE_CDN          => true,
            self::REDIRECT_WWW       => false,
            self::REDIRECT_HTTPS     => false,
            self::OPTIMIZER_OPTIONS  => [],
            self::SITEMAP_URL        => null,
        ];
    }

    public static function parseImports(array $config) : array
    {
        if (!isset($config['imports'])) {
            return $config;
        }

        $mergedConfig = [];

        foreach ($config['imports'] as $import) {
            $importConfig = self::parseImports(Yaml::parse(self::getConfigFile($import)->getContents()));

            $mergedConfig = array_replace_recursive($mergedConfig, $importConfig);
        }

        $mergedConfig = array_replace_recursive($mergedConfig, $config);

        return $mergedConfig;
    }

    public static function getConfigFile(string $path)
    {
        $pathParts = explode('/', $path);
        $configFileName = array_pop($pathParts);
        $configPath = implode('/', $pathParts) . '/';

        $configFiles = Finder::create()->files()->in($configPath)->name($configFileName)->depth(0)->getIterator();
        $configFiles->rewind();

        return $configFiles->current();
    }

    public static function flatten(array $config, string $prefix = '') : array
    {
        $result = [];

        foreach ($config as $key => $value) {
            $new_key = $prefix . (empty($prefix) ? '' : '.') . $key;

            if (is_array($value) && count($value)) {
                $result = array_merge($result, self::flatten($value, $new_key));
            } else {
                $result[$new_key] = $value;
            }
        }

        return $result;
    }
}

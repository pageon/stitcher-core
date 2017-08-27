<?php

namespace Brendt\Stitcher;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Yaml;

/**
 * Config helper class
 *
 * @package Brendt\Stitcher
 */
class Config
{
    const ASYNC = 'async';
    const ENVIRONMENT = 'environment';
    const DIRECTORIES_SRC = 'directories.src';
    const DIRECTORIES_PUBLIC = 'directories.public';
    const DIRECTORIES_CACHE = 'directories.cache';
    const DIRECTORIES_HTACCESS = 'directories.htaccess';
    const META = 'meta';
    const MINIFY = 'minify';
    const ENGINES_TEMPLATE = 'engines.template';
    const ENGINES_IMAGE = 'engines.image';
    const ENGINES_OPTIMIZER = 'engines.optimizer';
    const ENGINES_ASYNC = 'engines.async';
    const CDN = 'cdn';
    const CACHES_IMAGE = 'caches.image';
    const CACHES_CDN = 'caches.cdn';
    const REDIRECT_WWW = 'redirect.www';
    const REDIRECT_HTTPS = 'redirect.https';
    const OPTIMIZER_OPTIONS = 'optimizer.options';
    const SITEMAP_URL = 'sitemap.url';

    public static function getDefaults(): array {
        return [
            self::ASYNC                => true,
            self::ENVIRONMENT          => 'development',
            self::DIRECTORIES_SRC      => './src',
            self::DIRECTORIES_PUBLIC   => './public',
            self::DIRECTORIES_CACHE    => './.cache',
            self::DIRECTORIES_HTACCESS => './public/.htaccess',
            self::META                 => [],
            self::MINIFY               => false,
            self::ENGINES_TEMPLATE     => 'smarty',
            self::ENGINES_IMAGE        => 'gd',
            self::ENGINES_OPTIMIZER    => true,
            self::ENGINES_ASYNC        => true,
            self::CDN                  => [],
            self::CACHES_IMAGE         => true,
            self::CACHES_CDN           => true,
            self::REDIRECT_WWW         => false,
            self::REDIRECT_HTTPS       => false,
            self::OPTIMIZER_OPTIONS    => [],
            self::SITEMAP_URL          => null,
        ];
    }

    public static function parseImports(array $config): array {
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

    public static function getConfigFile(string $path) {
        $pathParts = explode('/', $path);
        $configFileName = array_pop($pathParts);
        $configPath = implode('/', $pathParts) . '/';

        $configFiles = Finder::create()->files()->in($configPath)->name($configFileName)->depth(0)->getIterator();
        $configFiles->rewind();

        return $configFiles->current();
    }

    public static function flatten(array $config, string $prefix = ''): array {
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

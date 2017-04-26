<?php

namespace Brendt\Stitcher;

use Brendt\Stitcher\Plugin\PluginConfiguration;
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

    /**
     * @param array $config
     *
     * @return array
     */
    public static function parseImports(array $config) : array {
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

    /**
     * @param string $path
     *
     * @return null|SplFileInfo
     */
    public static function getConfigFile(string $path) {
        $pathParts = explode('/', $path);
        $configFileName = array_pop($pathParts);
        $configPath = implode('/', $pathParts) . '/';

        $configFiles = Finder::create()->files()->in($configPath)->name($configFileName)->depth(0)->getIterator();
        $configFiles->rewind();

        return $configFiles->current();
    }

    /**
     * @param        $config
     * @param string $prefix
     *
     * @return array
     */
    public static function flatten(array $config, string $prefix = '') : array {
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

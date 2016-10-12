<?php

namespace brendt\stitcher;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class Config {

    protected static $config;

    public static function load($root = './') {
        $finder = new Finder();
        $configFiles = $finder->files()->in($root)->name('config.yml');
        $config = [];

        foreach ($configFiles as $configFile) {
            $config += Yaml::parse($configFile->getContents());
        }

        foreach ($config as $key => $value) {
            self::$config[$key] = $value;
        }
    }

    public static function set($key, $value) {
        $keys = explode('.', $key);
        $i = reset($keys);
        $configEntry = self::createConfigEntry($keys, $value);

        self::$config += $configEntry;
    }

    private static function createConfigEntry($keys, $value) {
        $configEntry = [];
        $key = array_shift($keys);

        if (count($keys)) {
            $configEntry[$key] = self::createConfigEntry($keys, $value);
        } else {
            $configEntry[$key] = $value;
        }

        return $configEntry;
    }

    public static function get($key) {
        $keys = explode('.', $key);
        $config = self::$config;

        reset($keys);

        while (($key = current($keys)) && isset($config[$key])) {
            $hasNext = next($keys);

            if (!$hasNext) {
                return $config[$key];
            }

            $config = $config[$key];
        }

        return null;
    }

}

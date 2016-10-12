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

        Config::set($config);
    }

    public static function set(array $config) {
        foreach ($config as $key => $value) {
            self::$config[$key] = $value;
        }
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

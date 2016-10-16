<?php

namespace brendt\stitcher;

use brendt\stitcher\factory\ProviderFactory;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Config {

    /**
     * @var array
     */
    protected static $config;

    /**
     * @var ContainerBuilder
     */
    protected static $container;

    public static function load($root = './') {
        $finder = new Finder();
        $configFiles = $finder->files()->in($root)->name('config.yml')->depth(1);
        $config = [];

        foreach ($configFiles as $configFile) {
            $config += Yaml::parse($configFile->getContents());
        }

        foreach ($config as $key => $value) {
            self::$config[$key] = $value;
        }

        self::$container = new ContainerBuilder();
        self::$container->register('factory.provider', ProviderFactory::class);
    }

    public static function getDependency($id) {
        return self::$container->get($id);
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

    public static function set($key, $value) {
        $keys = explode('.', $key);
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

}

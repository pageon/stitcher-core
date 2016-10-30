<?php

namespace brendt\stitcher;

use brendt\stitcher\engine\EnginePlugin;
use brendt\stitcher\factory\ProviderFactory;
use brendt\stitcher\factory\TemplateEngineFactory;
use Leafo\ScssPhp\Compiler;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use brendt\stitcher\engine\smarty\SmartyEngine;

/**
 * Class Config
 * @package brendt\stitcher
 */
class Config {

    /**
     * @var array
     */
    protected static $config;

    /**
     * @var ContainerBuilder
     */
    protected static $container;

    /**
     * @param string $root
     */
    public static function load($root = './', $name = 'config.yml') {
        $finder = new Finder();
        $configFiles = $finder->files()->in($root)->name($name);
        $config = [];

        foreach ($configFiles as $configFile) {
            $config += Yaml::parse($configFile->getContents());
        }

        foreach ($config as $key => $value) {
            self::$config[$key] = $value;
        }

        self::$container = new ContainerBuilder();
        self::$container->register('factory.provider', ProviderFactory::class);
        self::$container->register('factory.template.engine', TemplateEngineFactory::class);
        self::$container->register('engine.smarty', SmartyEngine::class);
        self::$container->register('engine.plugin', EnginePlugin::class);
        self::$container->register('engine.minify.css', CSSmin::class);
        self::$container->register('engine.sass', Compiler::class)
            ->addMethodCall('addImportPath', ['path' => Config::get('directories.src')]);
    }

    /**
     * @param $id
     *
     * @return object
     */
    public static function getDependency($id) {
        return self::$container->get($id);
    }

    /**
     * @param $key
     *
     * @return mixed|null
     */
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

    /**
     * @param $key
     * @param $value
     */
    public static function set($key, $value) {
        $keys = explode('.', $key);
        $configEntry = self::createConfigEntry($keys, $value);

        self::$config = array_merge(self::$config, $configEntry);
    }

    /**
     * @param $keys
     * @param $value
     *
     * @return array
     */
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

<?php

namespace brendt\stitcher;

use brendt\image\config\DefaultConfigurator;
use brendt\image\ResponsiveFactory;
use brendt\stitcher\factory\AdapterFactory;
use brendt\stitcher\factory\ParserFactory;
use brendt\stitcher\factory\TemplateEngineFactory;
use brendt\stitcher\template\smarty\SmartyEngine;
use brendt\stitcher\template\TemplatePlugin;
use CSSmin;
use Leafo\ScssPhp\Compiler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

/**
 * @todo Change parsing to work the other way around
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
     * @param string $name
     *
     * @todo Refactor `$root` and `$name` into one variable
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
        self::$container->register('factory.parser', ParserFactory::class);
        self::$container->register('factory.adapter', AdapterFactory::class);
        self::$container->register('factory.template.engine', TemplateEngineFactory::class);

        $imageConfig = new DefaultConfigurator([
            'driver'      => Config::get('engines.image'),
            'publicPath'  => Config::get('directories.public'),
            'sourcePath'  => Config::get('directories.src'),
            'enableCache' => Config::get('caches.image'),
        ]);

        self::$container->register('factory.image', ResponsiveFactory::class)
            ->addArgument($imageConfig);

        self::$container->register('engine.smarty', SmartyEngine::class);
        self::$container->register('engine.plugin', TemplatePlugin::class);
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
     *
     * @todo Refactor to work with dependencies
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
     * Reset the config
     */
    public static function reset() {
        self::$config = [];
    }

    public static function getConfig() {
        return self::$config;
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

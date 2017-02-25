<?php

namespace Brendt\Stitcher;

use Brendt\Image\Config\DefaultConfigurator;
use Brendt\Image\ResponsiveFactory;
use Brendt\Stitcher\Factory\AdapterFactory;
use Brendt\Stitcher\Factory\ParserFactory;
use Brendt\Stitcher\Factory\TemplateEngineFactory;
use Brendt\Stitcher\Template\smarty\SmartyEngine;
use Brendt\Stitcher\Template\TemplatePlugin;
use CSSmin;
use Leafo\ScssPhp\Compiler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Yaml;

/**
 * @todo Change parsing to work the other way around
 */
class Config
{

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
     */
    public static function load($root = './', $name = 'config.yml') {
        /** @var SplFileInfo[] $configFiles */
        $configFiles = Finder::create()->files()->in($root)->name($name);
        $config = [];

        foreach ($configFiles as $configFile) {
            $config += Yaml::parse($configFile->getContents());
        }

        foreach ($config as $key => $value) {
            self::$config[$key] = $value;
        }

        if (!self::get('directories.template')) {
            self::$config['directories']['template'] = self::get('directories.src') . '/template';
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
            'optimize'    => Config::get('engines.optimizer', false),
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
     * @param string      $key
     * @param null|string $default
     *
     * @return mixed|null
     * @todo Refactor to work with dependencies
     */
    public static function get(string $key, string $default = null) {
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

        return $default;
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

        if (!count($keys)) {
            return [$key => $value];
        }

        $configEntry[$key] = self::createConfigEntry($keys, $value);

        return $configEntry;
    }

}

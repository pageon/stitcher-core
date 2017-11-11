<?php

namespace Stitcher;

use Pageon\Config;
use Pageon\Html\Image\FixedWidthScaler;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class App
{
    /** @var ContainerBuilder */
    protected static $container;

    public static function init(string $basePath)
    {
        Config::init($basePath);

        self::$container = new ContainerBuilder();

        self::loadConfig();
        self::loadServices();
    }

    public static function get(string $id)
    {
        return self::$container->get($id);
    }

    protected static function loadConfig()
    {
        foreach (Config::all() as $key => $value) {
            self::$container->setParameter($key, $value);
        }
    }

    protected static function loadServices()
    {
        $loader = new YamlFileLoader(self::$container, new FileLocator(__DIR__));
        $loader->load('services.yaml');

        /** @var Definition $definition */
        foreach (self::$container->getDefinitions() as $id => $definition) {
            self::$container->setAlias($definition->getClass(), $id);
        }
    }
}

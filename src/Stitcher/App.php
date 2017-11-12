<?php

namespace Stitcher;

use Pageon\Config;
use Stitcher\Application\DevelopmentServer;
use Stitcher\Application\ProductionServer;
use Stitcher\Exception\InvalidConfiguration;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class App
{
    /** @var ContainerBuilder */
    protected static $container;

    public static function init()
    {
        Config::init();

        self::$container = new ContainerBuilder();

        self::loadConfig();
        self::loadServices();
    }

    public static function get(string $id)
    {
        return self::$container->get($id);
    }

    public static function developmentServer(): DevelopmentServer
    {
        try {
            /** @var DevelopmentServer $server */
            $server = self::get(DevelopmentServer::class);

            return $server;
        } catch (ParameterNotFoundException $e) {
            throw InvalidConfiguration::missingParameter($e->getKey());
        }
    }

    public static function productionServer(): ProductionServer
    {
        try {
            /** @var ProductionServer $server */
            $server = self::get(ProductionServer::class);

            return $server;
        } catch (ParameterNotFoundException $e) {
            throw InvalidConfiguration::missingParameter($e->getKey());
        }
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

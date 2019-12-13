<?php

namespace Stitcher;

use Illuminate\Support\Arr;
use Pageon\Config;
use Stitcher\Application\DevelopmentServer;
use Stitcher\Application\ProductionServer;
use Stitcher\Application\Router;
use Stitcher\Exception\InvalidConfiguration;
use Stitcher\Exception\InvalidPlugin;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class App
{
    /** @var ContainerBuilder */
    protected static $container;

    public static function init(): void
    {
        Config::init();

        self::$container = new ContainerBuilder();

        self::loadConfig(Config::all());

        self::loadServices('services.yaml');

        self::loadPlugins();

        self::loadRoutes();
    }

    /**
     * @param string $id
     *
     * @return mixed
     * @throws \Exception
     */
    public static function get(string $id)
    {
        return self::$container->get($id);
    }

    public static function developmentServer(): DevelopmentServer
    {
        try {
            return self::get(DevelopmentServer::class);
        } catch (ParameterNotFoundException $e) {
            throw InvalidConfiguration::missingParameter($e->getKey());
        }
    }

    public static function productionServer(): ProductionServer
    {
        try {
            return self::get(ProductionServer::class);
        } catch (ParameterNotFoundException $e) {
            throw InvalidConfiguration::missingParameter($e->getKey());
        }
    }

    public static function router(): Router
    {
        return self::get(Router::class);
    }

    protected static function loadConfig(array $config): void
    {
        foreach ($config as $key => $value) {
            self::$container->setParameter($key, $value);
        }
    }

    protected static function loadServices(string $servicesPath): void
    {
        $loader = new YamlFileLoader(self::$container, new FileLocator(__DIR__));

        $loader->load($servicesPath);

        /** @var Definition $definition */
        foreach (self::$container->getDefinitions() as $id => $definition) {
            if (! $definition->getClass()) {
                continue;
            }

            self::$container->setAlias($definition->getClass(), $id);
        }
    }

    protected static function loadPlugins(): void
    {
        foreach (Config::plugins() as $pluginClass) {
            if (!class_implements($pluginClass, Plugin::class)) {
                throw InvalidPlugin::doesntImplementPluginInterface($pluginClass);
            }

            self::loadPluginConfiguration($pluginClass);

            self::loadPluginServices($pluginClass);

            self::registerPluginDefinition($pluginClass);
        }
    }

    protected static function loadPluginConfiguration(string $pluginClass): void
    {
        $configurationPath = forward_static_call([$pluginClass, 'getConfigurationPath']);

        if (!$configurationPath) {
            return;
        }

        if (!file_exists($configurationPath)) {
            throw InvalidPlugin::configurationFileNotFound($pluginClass, $configurationPath);
        }

        $pluginConfiguration = require $configurationPath;

        if (! \is_array($pluginConfiguration)) {
            throw InvalidPlugin::configurationMustBeArray($pluginClass, $configurationPath);
        }

        self::loadConfig(Arr::dot($pluginConfiguration));
    }

    protected static function loadPluginServices(string $pluginClass): void
    {
        $servicesPath = forward_static_call([$pluginClass, 'getServicesPath']);

        if (!$servicesPath) {
            return;
        }

        if (!file_exists($servicesPath)) {
            throw InvalidPlugin::serviceFileNotFound($pluginClass, $servicesPath);
        }

        self::loadServices($servicesPath);
    }

    protected static function registerPluginDefinition(string $pluginClass): void
    {
        $definition = new Definition($pluginClass);

        $definition->setAutowired(true);

        self::$container->setDefinition($pluginClass, $definition);

        forward_static_call([$pluginClass, 'boot']);
    }

    protected static function loadRoutes(): void
    {
        $routeFile = File::path('src/routes.php');

        if (! file_exists($routeFile)) {
            return;
        }

        require_once $routeFile;
    }
}

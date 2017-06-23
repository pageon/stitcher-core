<?php

namespace Brendt\Stitcher;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;

class App
{
    protected static $container;
    protected static $configDefaults = [
        'async'                => true,
        'environment'          => 'development',
        'directories.src'      => './src',
        'directories.public'   => './public',
        'directories.cache'    => './.cache',
        'directories.htaccess' => './public/.htaccess',
        'meta'                 => [],
        'minify'               => false,
        'engines.template'     => 'smarty',
        'engines.image'        => 'gd',
        'engines.optimizer'    => true,
        'engines.async'        => true,
        'cdn'                  => [],
        'caches.image'         => true,
        'caches.cdn'           => true,
        'redirect.www'         => false,
        'redirect.https'       => false,
        'optimizer.options'    => [],
        'sitemap.url'          => null,
    ];

    public static function init(string $configPath = './config.yml', array $defaultConfig = []) : App {
        self::$container = new ContainerBuilder();

        $configFile = Config::getConfigFile($configPath);
        $parsedDefaultConfig = Yaml::parse($configFile->getContents());
        $parsedImportConfig = Config::parseImports($parsedDefaultConfig);

        $config = array_merge(
            self::$configDefaults,
            $parsedImportConfig,
            Config::flatten($parsedImportConfig),
            $defaultConfig
        );

        $config['directories.template'] = $config['directories.template'] ?? $config['directories.src'];

        foreach ($config as $key => $value) {
            self::$container->setParameter($key, $value);
        }

        $serviceLoader = new YamlFileLoader(self::$container, new FileLocator(__DIR__));
        $serviceLoader->load(__DIR__ . '/../../services.yml');
        self::loadPlugins($config, $serviceLoader);

        return new self();
    }

    public static function loadPlugins(array $config, YamlFileLoader $serviceLoader) {
        if (!isset($config['plugins'])) {
            return;
        }

        foreach ($config['plugins'] as $class) {
            self::loadPluginConfig(forward_static_call([$class, 'getConfigPath']));
            self::loadPluginServices(forward_static_call([$class, 'getServicesPath']), $serviceLoader);

            $pluginDefinition = new Definition($class);
            $pluginDefinition->setAutowired(true);
            self::$container->setDefinition($class, $pluginDefinition);
        }

        self::$container->compile();

        foreach ($config['plugins'] as $class) {
            self::$container->get($class);
        }
    }

    public static function loadPluginConfig($configFilePath) {
        if (!$configFilePath) {
            return;
        }

        $configFile = @file_get_contents((string) $configFilePath);
        $flatPluginConfig = Config::flatten(Yaml::parse($configFile));

        foreach ($flatPluginConfig as $key => $value) {
            if (!self::$container->hasParameter($key)) {
                self::$container->setParameter($key, $value);
            }
        }
    }

    public static function loadPluginServices($servicePath, YamlFileLoader $serviceLoader) {
        if (!$servicePath) {
            return;
        }

        $serviceLoader->load($servicePath);
    }

    public static function get(string $id) {
        return self::$container->get($id);
    }

    public static function getParameter(string $key) {
        return self::$container->getParameter($key);
    }

    public static function setApplication(string $id, Application $application) {
        self::$container->set($id, $application);
    }
}

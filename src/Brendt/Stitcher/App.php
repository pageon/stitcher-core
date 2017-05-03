<?php

namespace Brendt\Stitcher;

use Brendt\Stitcher\Plugin\PluginConfiguration;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;

class App
{
    /**
     * @var ContainerBuilder
     */
    protected static $container;

    /**
     * @var array
     */
    protected static $configDefaults = [
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
        'optimizer.options'    => [],
    ];

    /**
     * @param string $configPath
     * @param array  $defaultConfig
     *
     * @return App
     */
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
        $pluginConfigurationCollection = self::loadPlugins($config);
        self::loadPluginConfig($pluginConfigurationCollection);
        self::loadPluginServices($serviceLoader, $pluginConfigurationCollection);

        return new self();
    }

    /**
     * @param array $config
     *
     * @return PluginConfiguration[]
     */
    public static function loadPlugins(array $config) : array {
        if (!isset($config['plugins'])) {
            return [];
        }

        $pluginConfigurationCollection = [];

        foreach ($config['plugins'] as $pluginPath) {
            $pluginConfiguration = new PluginConfiguration($pluginPath);

            $pluginConfigurationCollection[] = $pluginConfiguration;
        }

        return $pluginConfigurationCollection;
    }

    /**
     * @param PluginConfiguration[] $pluginConfigurationCollection
     */
    public static function loadPluginConfig(array $pluginConfigurationCollection) {
        foreach ($pluginConfigurationCollection as $pluginConfig) {
            $flatPluginConfig = Config::flatten($pluginConfig->getConfig());

            foreach ($flatPluginConfig as $key => $value) {
                if (!self::$container->hasParameter($key)) {
                    self::$container->setParameter($key, $value);
                }
            }
        }
    }

    /**
     * @param YamlFileLoader        $serviceLoader
     * @param PluginConfiguration[] $pluginConfigurationCollection
     */
    public static function loadPluginServices(YamlFileLoader $serviceLoader, array $pluginConfigurationCollection) {
        foreach ($pluginConfigurationCollection as $pluginConfiguration) {
            $servicePath = $pluginConfiguration->getServicePath();

            if (!$servicePath) {
                continue;
            }

            $serviceLoader->load($servicePath);
            $plugin = $pluginConfiguration->getPlugin();
            if (method_exists($plugin, 'init')) {
                $plugin->init();
            }
        }
    }

    /**
     * @param string $id
     *
     * @return mixed
     */
    public static function get(string $id) {
        return self::$container->get($id);
    }

    /**
     * @param string      $id
     * @param Application $application
     */
    public static function setApplication(string $id, Application $application) {
        self::$container->set($id, $application);
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public static function getParameter(string $key) {
        return self::$container->getParameter($key);
    }

}

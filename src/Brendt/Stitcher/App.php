<?php

namespace Brendt\Stitcher;

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
        $parsedConfig = Yaml::parse($configFile->getContents());
        $parsedConfig = Config::parseImports($parsedConfig);

        $config = array_merge(
            self::$configDefaults,
            $parsedConfig,
            Config::flatten($parsedConfig),
            $defaultConfig
        );

        $config['directories.template'] = $config['directories.template'] ?? $config['directories.src'];

        foreach ($config as $key => $value) {
            self::$container->setParameter($key, $value);
        }

        $serviceLoader = new YamlFileLoader(self::$container, new FileLocator(__DIR__));
        $serviceLoader->load(__DIR__ . '/../../services.yml');

        return new self();
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
     * @param string $key
     *
     * @return mixed
     */
    public static function getParameter(string $key) {
        return self::$container->getParameter($key);
    }

}

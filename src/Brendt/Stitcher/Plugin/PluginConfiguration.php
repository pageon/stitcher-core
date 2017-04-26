<?php

namespace Brendt\Stitcher\Plugin;

use Brendt\Stitcher\Exception\ConfigurationException;
use Brendt\Stitcher\Exception\PluginException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Yaml;

class PluginConfiguration
{
    /**
     * @var Plugin
     */
    private $plugin;

    /**
     * @var string
     */
    private $servicePath;

    /**
     * @var array
     */
    private $config = [];

    public function __construct(string $path) {
        $pluginFiles = Finder::create()->files()->in($path)->name('*Plugin.php')->depth(1)->getIterator();
        $pluginFiles->rewind();
        /** @var SplFileInfo $pluginFile */
        $pluginFile = $pluginFiles->current();

        if (!$pluginFile) {
            throw ConfigurationException::pluginNotFound($path);
        }

        $this->loadPlugin($pluginFile, $path);
        $this->loadServices($this->plugin);
        $this->loadConfig($this->plugin);
    }

    private function loadPlugin(SplFileInfo $pluginFile, string $path) {
        require_once $pluginFile;

        $pluginClassName = pathinfo($pluginFile->getFilename(), PATHINFO_FILENAME);

        if (!class_exists($pluginClassName)) {
            throw PluginException::pluginNotFound($pluginClassName, $path);
        }

        $plugin = new $pluginClassName();

        if (!$plugin instanceof Plugin) {
            throw PluginException::invalidPlugin($pluginClassName);
        }

        $this->plugin = $plugin;
    }

    /**
     * @param Plugin $plugin
     */
    private function loadServices(Plugin $plugin) {
        $this->servicePath = $plugin->getServicesPath();
    }

    private function loadConfig(Plugin $plugin) {
        $configFile = @file_get_contents($plugin->getConfigPath());

        if (!$configFile) {
            return;
        }

        $this->config = Yaml::parse($configFile);
    }

    /**
     * @return Plugin
     */
    public function getPlugin() : Plugin {
        return $this->plugin;
    }

    /**
     * @return string
     */
    public function getServicePath() : string {
        return $this->servicePath;
    }

    /**
     * @return array
     */
    public function getConfig() : array {
        return $this->config;
    }

}

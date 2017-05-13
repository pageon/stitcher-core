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

    public function __construct(string $className) {
        $this->plugin = new $className();

        $this->loadServices($this->plugin);
        $this->loadConfig($this->plugin);
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
     * @return null|string
     */
    public function getServicePath() {
        return $this->servicePath;
    }

    /**
     * @return array
     */
    public function getConfig() : array {
        return $this->config;
    }

}

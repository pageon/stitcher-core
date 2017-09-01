<?php

namespace Brendt\Stitcher\Plugin;

use Symfony\Component\Yaml\Yaml;

class PluginConfiguration
{
    private $plugin;
    private $servicePath;
    private $config = [];

    public function __construct(string $className)
    {
        $this->plugin = new $className();

        $this->loadServices($this->plugin);
        $this->loadConfig($this->plugin);
    }

    private function loadServices(Plugin $plugin)
    {
        $this->servicePath = $plugin->getServicesPath();
    }

    private function loadConfig(Plugin $plugin)
    {
        $configFile = @file_get_contents($plugin->getConfigPath());

        if (!$configFile) {
            return;
        }

        $this->config = Yaml::parse($configFile);
    }

    public function getPlugin() : Plugin
    {
        return $this->plugin;
    }

    public function getServicePath()
    {
        return $this->servicePath;
    }

    public function getConfig() : array
    {
        return $this->config;
    }
}

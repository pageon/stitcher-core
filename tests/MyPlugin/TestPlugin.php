<?php

namespace MyPlugin;

use Brendt\Stitcher\App;
use Brendt\Stitcher\Factory\AdapterFactory;
use Brendt\Stitcher\Plugin\Plugin;

class TestPlugin implements Plugin
{
    /**
     * @var AdapterFactory
     */
    private $adapterFactory;

    public function __construct() {
        $this->adapterFactory = App::get('factory.adapter');
    }

    public function getConfigPath() {
        return __DIR__ . '/plugin.config.yml';
    }

    public function getServicesPath() {
        return __DIR__ . '/plugin.services.yml';
    }

    /**
     * @return AdapterFactory
     */
    public function getAdapterFactory() : AdapterFactory {
        return $this->adapterFactory;
    }
}

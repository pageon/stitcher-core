<?php

namespace MyPlugin;

use Brendt\Stitcher\Plugin\Plugin;

class TestPlugin implements Plugin
{
    public function init() {
        return;
    }

    public function getConfigPath() {
        return __DIR__ . '/plugin.config.yml';
    }

    public function getServicesPath() {
        return __DIR__ . '/plugin.services.yml';
    }
}

<?php

namespace Brendt\Stitcher\Plugin;

interface Plugin
{

    /**
     * Get the location of your plugin's `config.yml` file.
     *
     * @return null|string
     */
    public function getConfigPath();

    /**
     * Get the location of your plugin's `services.yml` file.
     *
     * @return null|string
     */
    public function getServicesPath();

}

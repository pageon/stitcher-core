<?php

use brendt\stitcher\Config;

/**
 * @return mixed
 *
 * @see \brendt\stitcher\template\EnginePlugin::meta()
 */
function smarty_function_meta() {
    $plugin = Config::getDependency('engine.plugin');

    return $plugin->meta();
}

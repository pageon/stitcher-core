<?php

use Brendt\Stitcher\Config;

/**
 * @return mixed
 *
 * @see \brendt\Stitcher\Template\EnginePlugin::meta()
 */
function smarty_function_meta() {
    $plugin = Config::getDependency('engine.plugin');

    return $plugin->meta();
}

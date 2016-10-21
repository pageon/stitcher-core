<?php

use brendt\stitcher\Config;

function smarty_function_meta($params, $template) {
    $plugin = Config::getDependency('engine.plugin');

    return $plugin->meta();
}

<?php

use brendt\stitcher\Config;

/**
 * @param $params
 *
 * @return mixed
 *
 * @see \brendt\stitcher\template\EnginePlugin::css()
 */
function smarty_function_css($params) {
    $plugin = Config::getDependency('engine.plugin');

    $src = isset($params['src']) ? $params['src'] : null;
    $inline = isset($params['inline']);

    return $plugin->css($src, $inline);
}

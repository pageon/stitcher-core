<?php

use brendt\stitcher\Config;

/**
 * @param $params
 *
 * @return mixed
 *
 * @see \brendt\stitcher\template\EnginePlugin::js()
 */
function smarty_function_js($params) {
    $plugin = Config::getDependency('engine.plugin');

    $src = isset($params['src']) ? $params['src'] : null;
    $inline = isset($params['inline']);
    $async = isset($params['async']);

    return $plugin->js($src, $inline, $async);
}

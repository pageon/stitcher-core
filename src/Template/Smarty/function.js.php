<?php

use Brendt\Stitcher\Config;

/**
 * @param $params
 *
 * @return mixed
 *
 * @see \Brendt\Stitcher\Template\EnginePlugin::js()
 */
function smarty_function_js($params) {
    $plugin = Config::getDependency('engine.plugin');

    $src = isset($params['src']) ? $params['src'] : null;
    $inline = isset($params['inline']);
    $async = isset($params['async']);

    return $plugin->js($src, $inline, $async);
}

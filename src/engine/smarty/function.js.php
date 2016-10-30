<?php

use brendt\stitcher\Config;

function smarty_function_js($params) {
    $plugin = Config::getDependency('engine.plugin');

    $src = isset($params['src']) ? $params['src'] : null;
    $inline = isset($params['inline']);
    $async = isset($params['async']);

    return $plugin->js($src, $inline, $async);
}

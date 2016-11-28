<?php

use brendt\stitcher\Config;

function smarty_function_image($params) {
    $plugin = Config::getDependency('engine.plugin');

    $src = isset($params['src']) ? $params['src'] : null;

    return $plugin->image($src);
}

<?php

use brendt\stitcher\Config;

function smarty_function_css($params, $template) {
    $plugin = Config::getDependency('engine.plugin');

    $src = isset($params['src']) ? $params['src'] : null;
    $inline = isset($params['inline']);

    return $plugin->css($src, $inline);
}

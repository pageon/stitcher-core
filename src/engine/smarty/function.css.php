<?php

use brendt\stitcher\Config;

function smarty_function_css($params, $template) {
    $plugin = Config::getDependency('engine.plugin');

    $src = isset($params['src']) ? $params['src'] : null;
    $isCritical = isset($params['critical']) ? $params['critical'] : null;

    return $plugin->css($src, $isCritical);
}

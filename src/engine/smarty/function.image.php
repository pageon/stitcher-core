<?php

use brendt\stitcher\Config;

/**
 * @param array $params
 * @param Smarty_Internal_Template $template
 */
function smarty_function_image(array $params, Smarty_Internal_Template $template) {
    $plugin = Config::getDependency('engine.plugin');
    $src = isset($params['src']) ? $params['src'] : null;
    $image = $plugin->image($src);

    $template->assign($params['var'], $image);
}

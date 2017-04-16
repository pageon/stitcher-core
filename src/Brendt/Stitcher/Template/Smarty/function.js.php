<?php

use Brendt\Stitcher\Stitcher;
use Brendt\Stitcher\Template\TemplatePlugin;

/**
 * @param $params
 *
 * @return mixed
 *
 * @see \Brendt\Stitcher\Template\EnginePlugin::js()
 */
function smarty_function_js($params) {
    /** @var TemplatePlugin $plugin */
    $plugin = Stitcher::get('service.template.plugin');

    $src = isset($params['src']) ? $params['src'] : null;
    $inline = isset($params['inline']);
    $async = isset($params['async']);

    return $plugin->js($src, $inline, $async);
}

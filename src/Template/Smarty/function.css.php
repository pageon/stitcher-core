<?php

use Brendt\Stitcher\Stitcher;
use Brendt\Stitcher\Template\TemplatePlugin;

/**
 * @param $params
 *
 * @return mixed
 *
 * @see \Brendt\Stitcher\Template\EnginePlugin::css()
 */
function smarty_function_css($params) {
    /** @var TemplatePlugin $plugin */
    $plugin = Stitcher::get('engine.plugin');

    $src = isset($params['src']) ? $params['src'] : null;
    $inline = isset($params['inline']);

    return $plugin->css($src, $inline);
}

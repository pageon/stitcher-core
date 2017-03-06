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
function smarty_function_file($params) {
    /** @var TemplatePlugin $plugin */
    $plugin = Stitcher::get('engine.plugin');

    $src = isset($params['src']) ? $params['src'] : null;

    return $plugin->file($src);
}

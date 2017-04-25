<?php

use Brendt\Stitcher\App;
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
    $plugin = App::get('service.template.plugin');

    $src = isset($params['src']) ? $params['src'] : null;
    $inline = isset($params['inline']);
    $push = isset($params['push']);

    return $plugin->css($src, $inline, $push);
}

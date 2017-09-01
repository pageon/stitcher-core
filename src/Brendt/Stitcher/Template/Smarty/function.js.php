<?php

use Brendt\Stitcher\App;
use Brendt\Stitcher\Template\TemplatePlugin;

/**
 * @param $params
 *
 * @return mixed
 *
 * @see \Brendt\Stitcher\Template\EnginePlugin::js()
 */
function smarty_function_js($params)
{
    /** @var TemplatePlugin $plugin */
    $plugin = App::get('service.template.plugin');

    $src = isset($params['src']) ? $params['src'] : null;
    $inline = isset($params['inline']);
    $async = isset($params['async']);
    $push = isset($params['push']);

    return $plugin->js($src, $inline, $async, $push);
}

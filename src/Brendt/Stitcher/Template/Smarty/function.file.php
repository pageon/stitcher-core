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
function smarty_function_file($params)
{
    /** @var TemplatePlugin $plugin */
    $plugin = App::get('service.template.plugin');

    $src = isset($params['src']) ? $params['src'] : null;
    $push = isset($params['push']);

    return $plugin->file($src, $push);
}

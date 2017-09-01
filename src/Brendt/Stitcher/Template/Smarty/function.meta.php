<?php

use Brendt\Stitcher\App;
use Brendt\Stitcher\Template\TemplatePlugin;

/**
 * @param array $params
 *
 * @return mixed
 * @see \Brendt\Stitcher\Template\EnginePlugin::meta()
 */
function smarty_function_meta(array $params)
{
    /** @var TemplatePlugin $plugin */
    $plugin = App::get('service.template.plugin');

    $extra = (array) ($params['extra'] ?? []);

    return $plugin->meta($extra);
}

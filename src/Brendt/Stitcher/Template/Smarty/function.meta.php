<?php

use Brendt\Stitcher\Stitcher;
use Brendt\Stitcher\Template\TemplatePlugin;
use Brendt\Html\Meta\Meta;

/**
 * @param array $params
 *
 * @return mixed
 * @see \Brendt\Stitcher\Template\EnginePlugin::meta()
 */
function smarty_function_meta(array $params) {
    /** @var TemplatePlugin $plugin */
    $plugin = Stitcher::get('service.template.plugin');

    $extra = (array) ($params['extra'] ?? []);

    return $plugin->meta($extra);
}

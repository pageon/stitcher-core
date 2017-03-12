<?php

use Brendt\Stitcher\Stitcher;
use Brendt\Stitcher\Template\TemplatePlugin;

/**
 * @param array $params
 *
 * @return mixed
 * @see \Brendt\Stitcher\Template\EnginePlugin::meta()
 */
function smarty_function_meta(array $params) {
    /** @var TemplatePlugin $plugin */
    $plugin = Stitcher::get('service.template.plugin');

    $meta = (array) ($params['meta'] ?? []);

    return $plugin->meta($meta);
}

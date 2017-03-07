<?php

use Brendt\Stitcher\Stitcher;
use Brendt\Stitcher\Template\TemplatePlugin;

/**
 * @return mixed
 *
 * @see \Brendt\Stitcher\Template\EnginePlugin::meta()
 */
function smarty_function_meta() {
    /** @var TemplatePlugin $plugin */
    $plugin = Stitcher::get('service.template.plugin');

    return $plugin->meta();
}

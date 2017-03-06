<?php

use Brendt\Stitcher\Stitcher;
use Brendt\Stitcher\Template\TemplatePlugin;

/**
 * @param array                    $params
 * @param Smarty_Internal_Template $template
 *
 * @return mixed
 *
 * @see \Brendt\Stitcher\Template\EnginePlugin::image()
 */
function smarty_function_image(array $params, Smarty_Internal_Template $template) {
    /** @var TemplatePlugin $plugin */
    $plugin = Stitcher::get('engine.plugin');

    $src = isset($params['src']) ? $params['src'] : null;
    $image = $plugin->image($src);

    $template->assign($params['var'], $image);
}

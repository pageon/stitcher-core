<?php

use Brendt\Stitcher\App;
use Brendt\Stitcher\Template\TemplatePlugin;

/**
 * @param array                    $params
 * @param Smarty_Internal_Template $template
 *
 * @return mixed
 *
 * @see \Brendt\Stitcher\Template\EnginePlugin::image()
 */
function smarty_function_image(array $params, Smarty_Internal_Template $template)
{
    /** @var TemplatePlugin $plugin */
    $plugin = App::get('service.template.plugin');

    $src = isset($params['src']) ? $params['src'] : null;
    $push = isset($params['push']);
    $image = $plugin->image($src, $push);

    $template->assign($params['var'], $image);
}

<?php

namespace Brendt\Stitcher\Template\Smarty;

use \Smarty;
use Brendt\Stitcher\Template\TemplateEngine;
use Symfony\Component\Finder\SplFileInfo;

/**
 * The Smarty template engine.
 */
class SmartyEngine extends Smarty implements TemplateEngine
{
    public function __construct($templateDir = './src', $cacheDir = './.cache') {
        parent::__construct();

        $this->addTemplateDir($templateDir);
        $this->setCompileDir($cacheDir);
        $this->addPluginsDir([__DIR__]);

        $this->caching = false;
    }

    public function renderTemplate(SplFileInfo $template) {
        return $this->fetch($template->getRealPath());
    }

    public function addTemplateVariables(array $variables) {
        foreach ($variables as $name => $variable) {
            $this->assign($name, $variable);
        }

        return $this;
    }

    public function clearTemplateVariables() {
        $this->clearAllAssign();

        return $this;
    }

    public function addTemplateVariable($name, $value) {
        $this->assign($name, $value);

        return $this;
    }

    public function hasTemplateVariable(string $name) : bool {
        return $this->getTemplateVars($name) != null;
    }

    public function clearTemplateVariable($variable) {
        $this->clearAssign($variable);

        return $this;
    }

    public function getTemplateExtension() {
        return 'tpl';
    }
}

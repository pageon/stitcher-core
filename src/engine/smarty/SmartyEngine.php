<?php

namespace brendt\stitcher\engine\smarty;

use brendt\stitcher\Config;
use brendt\stitcher\engine\TemplateEngine;
use Symfony\Component\Finder\Finder;
use \Smarty;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class SmartyEngine
 * @package brendt\stitcher\engine\smarty
 */
class SmartyEngine extends Smarty implements TemplateEngine {

    /**
     * SmartyEngine constructor.
     */
    public function __construct() {
        parent::__construct();

        $templateFolder = Config::get('directories.template') ? Config::get('directories.template') : Config::get('directories.src') . '/template';
        $this->addTemplateDir($templateFolder);

        $this->setCompileDir(Config::get('directories.cache'));
        $this->addPluginsDir(__DIR__);

        $this->caching = false;
    }

    /**
     * @param SplFileInfo $template
     *
     * @return string
     */
    public function renderTemplate(SplFileInfo $template) {
        return $this->fetch($template->getRealPath());
    }

    /**
     * @param array $variables
     *
     * @return SmartyEngine $this
     */
    public function addTemplateVariables(array $variables) {
        foreach ($variables as $name => $variable) {
            $this->assign($name, $variable);
        }

        return $this;
    }

    /**
     * @return SmartyEngine $this
     */
    public function clearTemplateVariables() {
        $this->clearAllAssign();

        return $this;
    }

    /**
     * @param $name
     * @param $value
     *
     * @return SmartyEngine
     */
    public function addTemplateVariable($name, $value) {
        $this->assign($name, $value);

        return $this;
    }

    /**
     * @param $variable
     *
     * @return SmartyEngine
     */
    public function clearTemplateVariable($variable) {
        $this->clearAssign($variable);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTemplateExtension() {
        return 'tpl';
    }
}

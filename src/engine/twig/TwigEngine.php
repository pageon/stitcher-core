<?php

namespace brendt\stitcher\engine\twig;

use brendt\stitcher\engine\EnginePlugin;
use Twig_Environment;
use Twig_Loader_Filesystem;
use brendt\stitcher\Config;
use brendt\stitcher\engine\TemplateEngine;
use Symfony\Component\Finder\SplFileInfo;

class TwigEngine extends Twig_Environment implements TemplateEngine {

    /**
     * @var array
     */
    private $variables = [];

    /**
     * TwigEngine constructor.
     */
    public function __construct() {
        $templateFolder = Config::get('directories.template') ? Config::get('directories.template') : Config::get('directories.src') . '/template';
        $loader = new Twig_Loader_Filesystem($templateFolder);

        parent::__construct($loader, [
            'cache' => false,
        ]);

        $plugin = Config::getDependency('engine.plugin');
        $this->addFunction(new \Twig_SimpleFunction('meta', [$plugin, 'meta'], [
            'is_safe' => ['html']
        ]));
        $this->addFunction(new \Twig_SimpleFunction('css', [$plugin, 'css'], [
            'is_safe' => ['html']
        ]));
        $this->addFunction(new \Twig_SimpleFunction('js', [$plugin, 'js'], [
            'is_safe' => ['html']
        ]));
        $this->addFunction(new \Twig_SimpleFunction('image', [$plugin, 'image']));
    }

    public function thumbnail() {
        return "<meta>";
    }

    /**
     * Render the template and return output HTML
     *
     * @param SplFileInfo $template
     *
     * @return string
     */
    public function renderTemplate(SplFileInfo $template) {
        return $this->render($template->getRelativePathname(), $this->variables);
    }

    /**
     * Add an array of template variables
     *
     * @param array $variables
     *
     * @return TemplateEngine
     */
    public function addTemplateVariables(array $variables) {
        $this->variables += $variables;

        return $this;
    }

    /**
     * Clear all template variables
     *
     * @return TemplateEngine
     */
    public function clearTemplateVariables() {
        $this->variables = [];

        return $this;
    }

    /**
     * @param $name
     * @param $value
     *
     * @return TemplateEngine
     */
    public function addTemplateVariable($name, $value) {
        $this->variables[$name] = $value;

        return $this;
    }

    /**
     * @param $variable
     *
     * @return TemplateEngine
     */
    public function clearTemplateVariable($variable) {
        unset($this->variables[$variable]);

        return $this;
    }

    /**
     * @return string
     */
    public function getTemplateExtension() {
        return 'html';
    }
}

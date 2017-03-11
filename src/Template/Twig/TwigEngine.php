<?php

namespace Brendt\Stitcher\Template\Twig;

use Brendt\Stitcher\Config;
use Brendt\Stitcher\Template\TemplateEngine;
use Brendt\Stitcher\Template\TemplatePlugin;
use Symfony\Component\Finder\SplFileInfo;
use Twig_Environment;
use Twig_Loader_Filesystem;

/**
 * The Twig template engine.
 */
class TwigEngine extends Twig_Environment implements TemplateEngine
{

    /**
     * An array of template variables available when rendering a template.
     *
     * @var array
     */
    private $variables = [];

    /**
     * Create a new Twig engine and add the Stitcher specific template functions.
     *
     * @param string         $templateDir
     * @param TemplatePlugin $templatePlugin
     */
    public function __construct(string $templateDir, TemplatePlugin $templatePlugin) {
        $loader = new Twig_Loader_Filesystem($templateDir);

        parent::__construct($loader, [
            'cache' => false,
        ]);

        $this->addFunction(new \Twig_SimpleFunction('meta', [$templatePlugin, 'meta'], ['is_safe' => ['html'],]));
        $this->addFunction(new \Twig_SimpleFunction('css', [$templatePlugin, 'css'], ['is_safe' => ['html'],]));
        $this->addFunction(new \Twig_SimpleFunction('js', [$templatePlugin, 'js'], ['is_safe' => ['html'],]));
        $this->addFunction(new \Twig_SimpleFunction('image', [$templatePlugin, 'image']));
        $this->addFunction(new \Twig_SimpleFunction('file', [$templatePlugin, 'file']));
    }

    /**
     * {@inheritdoc}
     */
    public function renderTemplate(SplFileInfo $template) {
        return $this->render($template->getRelativePathname(), $this->variables);
    }

    /**
     * {@inheritdoc}
     */
    public function addTemplateVariables(array $variables) {
        $this->variables += $variables;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function clearTemplateVariables() {
        $this->variables = [];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addTemplateVariable($name, $value) {
        $this->variables[$name] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function clearTemplateVariable($variable) {
        unset($this->variables[$variable]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateExtension() {
        return 'html';
    }
}

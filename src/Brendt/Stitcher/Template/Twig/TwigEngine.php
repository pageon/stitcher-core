<?php

namespace Brendt\Stitcher\Template\Twig;

use Brendt\Stitcher\Lib\Browser;
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
    private $variables = [];

    public function __construct(Browser $browser, TemplatePlugin $templatePlugin)
    {
        $loader = new Twig_Loader_Filesystem($browser->getTemplateDir());

        parent::__construct($loader, [
            'cache' => false,
        ]);

        $this->addFunction(new \Twig_SimpleFunction('meta', [$templatePlugin, 'meta'], ['is_safe' => ['html'],]));
        $this->addFunction(new \Twig_SimpleFunction('css', [$templatePlugin, 'css'], ['is_safe' => ['html'],]));
        $this->addFunction(new \Twig_SimpleFunction('js', [$templatePlugin, 'js'], ['is_safe' => ['html'],]));
        $this->addFunction(new \Twig_SimpleFunction('image', [$templatePlugin, 'image']));
        $this->addFunction(new \Twig_SimpleFunction('file', [$templatePlugin, 'file']));
    }

    public function renderTemplate(SplFileInfo $template)
    {
        return $this->render($template->getRelativePathname(), $this->variables);
    }

    public function addTemplateVariables(array $variables)
    {
        $this->variables += $variables;

        return $this;
    }

    public function hasTemplateVariable(string $name) : bool
    {
        return isset($this->variables[$name]);
    }

    public function clearTemplateVariables()
    {
        $this->variables = [];

        return $this;
    }

    public function addTemplateVariable($name, $value)
    {
        $this->variables[$name] = $value;

        return $this;
    }

    public function clearTemplateVariable($variable)
    {
        unset($this->variables[$variable]);

        return $this;
    }

    public function getTemplateExtensions() : array
    {
        return ['html', 'twig'];
    }
}

<?php

namespace Stitcher\Renderer;

use Stitcher\Renderer;
use Symfony\Component\Filesystem\Filesystem;
use Twig_Environment;
use Twig_Function;

class TwigRenderer extends Twig_Environment implements Renderer
{
    public function __construct(string $templateDirectory)
    {
        $fs = new Filesystem();
        if (! $fs->exists($templateDirectory)) {
            $fs->mkdir($templateDirectory);
        }

        $loader = new \Twig_Loader_Filesystem($templateDirectory);

        parent::__construct($loader);
    }

    public static function make(string $templateDirectory): TwigRenderer
    {
        return new self($templateDirectory);
    }

    public function renderTemplate(string $path, array $variables): string
    {
        return $this->render($path, $variables);
    }

    public function customExtension(string $name, callable $function): void
    {
        $this->addFunction(new Twig_Function($name, $function));
    }
}

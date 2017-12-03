<?php

namespace Stitcher\Renderer;

use Stitcher\Renderer;
use Symfony\Component\Filesystem\Filesystem;
use Twig_Environment;

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

    public function customExtension(Extension $extension): void
    {
        $this->addGlobal($extension->name(), $extension);
    }
}

<?php

namespace Stitcher\Renderer;

use Stitcher\Exception\InvalidConfiguration;
use Symfony\Component\Filesystem\Filesystem;
use Twig_Environment;
use Twig_Error_Loader;

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
        try {
            return $this->render($path, $variables);
        } catch (Twig_Error_Loader $e) {
            throw InvalidConfiguration::templateNotFound($path);
        }
    }

    public function customExtension(Extension $extension): void
    {
        $this->addGlobal($extension->name(), $extension);
    }
}

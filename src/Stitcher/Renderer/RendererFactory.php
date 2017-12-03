<?php

namespace Stitcher\Renderer;

use Stitcher\DynamicFactory;
use Stitcher\Renderer;

class RendererFactory extends DynamicFactory
{
    protected $templateDirectory;
    protected $rendererConfiguration;
    protected $extensions = [];

    public function __construct(string $templateDirectory, ?string $rendererConfiguration = 'twig')
    {
        $this->templateDirectory = $templateDirectory;
        $this->rendererConfiguration = $rendererConfiguration;

        $this->setTwigRule();
    }

    public static function make(string $templateDirectory, ?string $renderer = 'twig'): RendererFactory
    {
        return new self($templateDirectory, $renderer);
    }

    public function addExtension(Extension $extension)
    {
        $this->extensions[$extension->name()] = $extension;
    }

    public function create(): ?Renderer
    {
        foreach ($this->getRules() as $rule) {
            $templateRenderer = $rule($this->rendererConfiguration);

            if ($templateRenderer) {
                $this->loadExtensions($templateRenderer);

                return $templateRenderer;
            }
        }

        return null;
    }

    protected function loadExtensions(Renderer $renderer)
    {
        foreach ($this->extensions as $extension) {
            $renderer->customExtension($extension);
        }
    }

    private function setTwigRule(): void
    {
        $this->setRule(TwigRenderer::class, function ($value) {
            if ($value === 'twig') {
                return TwigRenderer::make($this->templateDirectory);
            }

            return null;
        });
    }
}

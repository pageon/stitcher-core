<?php

namespace Stitcher\Renderer;

use Stitcher\DynamicFactory;
use Stitcher\Renderer;

class RendererFactory extends DynamicFactory
{
    private $templateDirectory;
    private $renderer;

    public function __construct(string $templateDirectory, ?string $renderer = 'twig')
    {
        $this->templateDirectory = $templateDirectory;
        $this->renderer = $renderer;

        $this->setTwigRule();
    }

    public static function make(string $templateDirectory, ?string $renderer = 'twig'): RendererFactory
    {
        return new self($templateDirectory, $renderer);
    }

    public function create(): ?Renderer
    {
        foreach ($this->getRules() as $rule) {
            $templateRenderer = $rule($this->renderer);

            if ($templateRenderer) {
                return $templateRenderer;
            }
        }

        return null;
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

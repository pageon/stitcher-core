<?php

namespace Stitcher\Renderer;

use Stitcher\DynamicFactory;
use Stitcher\Renderer;

class RendererFactory extends DynamicFactory
{
    private $templateDirectory;

    public function __construct(string $templateDirectory)
    {
        $this->templateDirectory = $templateDirectory;

        $this->setTwigRule();
    }

    public static function make(string $templateDirectory): RendererFactory
    {
        return new self($templateDirectory);
    }

    public function create($value): ?Renderer
    {
        foreach ($this->getRules() as $rule) {
            $templateRenderer = $rule($value);

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

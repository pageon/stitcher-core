<?php

namespace Stitcher\Test;

use Leafo\ScssPhp\Compiler;
use Pageon\Html\Image\FixedWidthScaler;
use Pageon\Html\Image\ImageFactory;
use Parsedown;
use Stitcher\File;
use Stitcher\Page\Adapter\AdapterFactory;
use Stitcher\Page\PageFactory;
use Stitcher\Page\PageParser;
use Stitcher\Page\PageRenderer;
use Stitcher\Renderer\Extension\Css;
use Stitcher\Renderer\Extension\Js;
use Stitcher\Renderer\TwigRenderer;
use Stitcher\Variable\VariableFactory;
use Stitcher\Variable\VariableParser;
use Symfony\Component\Yaml\Yaml;

trait CreateStitcherObjects
{
    protected function createPageRenderer() : PageRenderer
    {
        $renderer = TwigRenderer::make(File::path('/resources/view'));

        return PageRenderer::make(
            $renderer
        );
    }

    protected function createPageParser(VariableParser $variableParser = null) : PageParser
    {
        $variableParser = $variableParser ?? $this->createVariableParser(File::path());

        return PageParser::make(
            PageFactory::make($variableParser),
            AdapterFactory::make($variableParser)
        );
    }

    protected function createVariableParser(string $sourceDirectory = null) : VariableParser
    {
        return VariableParser::make(
            VariableFactory::make()
                ->setMarkdownParser(new Parsedown())
                ->setYamlParser(new Yaml())
                ->setImageParser($this->createImageFactory($sourceDirectory))
        );
    }

    protected function createPageFactory(VariableParser $variableParser) : PageFactory
    {
        return PageFactory::make($variableParser);
    }

    protected function createAdapterFactory(VariableParser $variableParser) : AdapterFactory
    {
        return AdapterFactory::make($variableParser);
    }

    protected function createImageFactory($sourceDirectory = null): ImageFactory
    {
        $sourceDirectory = $sourceDirectory ?? File::path();
        $publicPath = File::path('public');

        return ImageFactory::make($sourceDirectory, $publicPath, FixedWidthScaler::make([
            300, 500,
        ]));
    }

    protected function createVariableFactory(VariableParser $variableParser = null) : VariableFactory
    {
        $variableParser = $variableParser ?? $this->createVariableParser();

        $factory = VariableFactory::make()
            ->setVariableParser($variableParser)
            ->setMarkdownParser(new Parsedown())
            ->setYamlParser(new Yaml())
            ->setImageParser($this->createImageFactory());

        return $factory;
    }
}

<?php

namespace Stitcher\Test;

use Pageon\Html\Image\FixedWidthScaler;
use Pageon\Html\Image\ImageFactory;
use Pageon\Html\SiteMap;
use Pageon\Lib\Markdown\MarkdownParser;
use Stitcher\File;
use Stitcher\Page\Adapter\AdapterFactory;
use Stitcher\Page\PageFactory;
use Stitcher\Page\PageParser;
use Stitcher\Page\PageRenderer;
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
                ->setMarkdownParser($this->createMarkdownParser())
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
            ->setMarkdownParser($this->createMarkdownParser())
            ->setYamlParser(new Yaml())
            ->setImageParser($this->createImageFactory());

        return $factory;
    }

    protected function createMarkdownParser(): MarkdownParser
    {
        return new MarkdownParser($this->createImageFactory());
    }

    protected function createSiteMap(): SiteMap
    {
        return new SiteMap('https://www.stitcher.io');
    }
}

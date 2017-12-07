<?php

namespace Stitcher;

use Pageon\Html\Image\FixedWidthScaler;
use Pageon\Html\Image\ImageFactory;
use Stitcher\Application\DevelopmentServer;
use Stitcher\Command\Parse;
use Stitcher\Command\PartialParse;
use Stitcher\Page\Adapter\AdapterFactory;
use Stitcher\Page\PageFactory;
use Stitcher\Page\PageParser;
use Stitcher\Page\PageRenderer;
use Stitcher\Renderer\Renderer;
use Stitcher\Renderer\RendererFactory;
use Stitcher\Test\CreateStitcherFiles;
use Stitcher\Test\StitcherTest;
use Stitcher\Variable\VariableFactory;
use Stitcher\Variable\VariableParser;

class AppTest extends StitcherTest
{
    use CreateStitcherFiles;

    /** @test */
    public function test_init()
    {
        App::init(__DIR__ . '/../resources');

        $servicesToTest = [
            VariableFactory::class,
            VariableParser::class,
            FixedWidthScaler::class,
            ImageFactory::class,
            AdapterFactory::class,
            PageFactory::class,
            RendererFactory::class,
            PageParser::class,
            PageRenderer::class,
            Parse::class,
            PartialParse::class,
            DevelopmentServer::class
        ];

        foreach ($servicesToTest as $class) {
            $this->assertServiceRegistered($class);
        }

        $this->assertInstanceOf(Renderer::class, App::get('renderer'));
    }

    private function assertServiceRegistered(string $class)
    {
        $this->assertInstanceOf($class, App::get($class));
    }
}

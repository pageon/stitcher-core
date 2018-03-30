<?php

namespace Stitcher;

use Pageon\Html\Image\FilesizeScaler;
use Pageon\Html\Image\FixedWidthScaler;
use Pageon\Html\Image\ImageFactory;
use Stitcher\Application\DevelopmentServer;
use Stitcher\Application\ProductionServer;
use Stitcher\Task\Parse;
use Stitcher\Task\PartialParse;
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
    public function initialise_test(): void
    {
        App::init();

        $servicesToTest = [
            VariableFactory::class,
            VariableParser::class,
            FilesizeScaler::class,
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

    /** @test */
    public function it_can_get_the_development_server(): void
    {
        App::init();

        $this->assertInstanceOf(DevelopmentServer::class, App::developmentServer());
    }

    /** @test */
    public function it_can_get_the_production_server(): void
    {
        App::init();

        $this->assertInstanceOf(ProductionServer::class, App::productionServer());
    }

    private function assertServiceRegistered(string $class): void
    {
        $this->assertInstanceOf($class, App::get($class));
    }
}

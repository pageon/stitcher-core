<?php

namespace Stitcher\Renderer;

use Stitcher\File;
use Stitcher\Test\StitcherTest;

class RendererFactoryTest extends StitcherTest
{
    /** @test */
    public function it_creates_the_correct_template_renderer(): void
    {
        $factory = RendererFactory::make(File::path('templates'), 'twig');

        $this->assertInstanceOf(TwigRenderer::class, $factory->create());
    }
}

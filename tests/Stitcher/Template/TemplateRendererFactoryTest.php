<?php

namespace Stitcher\Renderer;

use Stitcher\File;
use Stitcher\Test\StitcherTest;

class TemplateRendererFactoryTest extends StitcherTest
{
    /** @test */
    public function it_creates_the_correct_template_renderer()
    {
        $factory = RendererFactory::make(File::path('templates'));

        $this->assertInstanceOf(TwigRenderer::class, $factory->create('twig'));
    }
}

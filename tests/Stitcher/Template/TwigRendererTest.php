<?php

namespace Stitcher\Renderer;

use Stitcher\File;
use Stitcher\Test\CreateStitcherFiles;
use Stitcher\Test\StitcherTest;

class TwigRendererTest extends StitcherTest
{
    use CreateStitcherFiles;

    /** @test */
    public function it_can_render_a_template()
    {
        $renderer = TwigRenderer::make(File::path('resources/view'));
        $this->createAllTemplates();

        $html = $renderer->renderTemplate('index.twig', [
            'variable' => 'hello world'
        ]);

        $this->assertContains('hello world', $html);
    }
}

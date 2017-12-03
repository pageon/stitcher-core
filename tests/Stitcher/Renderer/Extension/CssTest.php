<?php

namespace Stitcher\Renderer\Extension;

use Leafo\ScssPhp\Compiler as Sass;
use Stitcher\Test\StitcherTest;

class CssTest extends StitcherTest
{
    /** @test */
    public function it_moves_a_css_file()
    {
        $css = $this->createExtension();

        $css->handle('/css/normal.css');
    }

    private function createExtension(): Css
    {
        return new Css(new Sass());
    }
}

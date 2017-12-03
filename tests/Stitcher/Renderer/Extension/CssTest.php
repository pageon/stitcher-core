<?php

namespace Stitcher\Renderer\Extension;

use Leafo\ScssPhp\Compiler as Sass;
use Stitcher\File;
use Stitcher\Test\StitcherTest;

class CssTest extends StitcherTest
{
    /** @test */
    public function it_moves_a_css_file()
    {
        $css = $this->createExtension();

        $css->handle('/resources/css/normal.css');

        $this->assertNotNull(File::read('public/resources/css/normal.css'));
        $this->assertContains('body', File::read('public/resources/css/normal.css'));
    }

    /** @test */
    public function it_moves_and_parses_a_scss_file()
    {
        $css = $this->createExtension();

        $css->handle('/resources/css/scss_file.scss');

        $this->assertNotNull(File::read('public/resources/css/scss_file.css'));
        $this->assertContains('body h1', File::read('public/resources/css/scss_file.css'));
    }

    private function createExtension(): Css
    {
        return new Css(
            File::path(),
            File::path('public'),
            new Sass()
        );
    }
}

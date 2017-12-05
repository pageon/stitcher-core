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

        $css->parseSource('/resources/css/normal.css');

        $this->assertNotNull(File::read('public/resources/css/normal.css'));
        $this->assertContains('body', File::read('public/resources/css/normal.css'));
    }

    /** @test */
    public function it_moves_and_parses_a_scss_file()
    {
        $css = $this->createExtension();

        $css->parseSource('/resources/css/scss_file.scss');

        $this->assertNotNull(File::read('public/resources/css/scss_file.css'));
        $this->assertContains('body h1', File::read('public/resources/css/scss_file.css'));
    }

    /** @test */
    public function test_inline()
    {
        $css = $this->createExtension();

        $style = $css->inline('/resources/css/scss_file.scss');

        $this->assertContains('<style', $style);
        $this->assertContains('body h1', $style);
    }

    /** @test */
    public function test_link()
    {
        $css = $this->createExtension();

        $style = $css->link('/resources/css/normal.css');

        $this->assertContains('<link', $style);
        $this->assertContains('rel="stylesheet"', $style);
        $this->assertContains('href="/resources/css/normal.css"', $style);
    }

    private function createExtension(): Css
    {
        return new Css(
            File::path('public'),
            new Sass()
        );
    }
}

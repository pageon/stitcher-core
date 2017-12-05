<?php

namespace Stitcher\Renderer\Extension;

use Stitcher\File;
use Stitcher\Test\StitcherTest;

class JsTest extends StitcherTest
{
    /** @test */
    public function it_moves_a_js_file()
    {
        $js = $this->createExtension();

        $js->parseSource('/resources/js/main.js');

        $this->assertNotNull(File::read('public/resources/js/main.js'));
        $this->assertContains('console.log', File::read('public/resources/js/main.js'));
    }

    /** @test */
    public function test_inline()
    {
        $js = $this->createExtension();

        $script = $js->inline('/resources/js/main.js');

        $this->assertContains('<script', $script);
        $this->assertContains('console.log', $script);
    }

    /** @test */
    public function test_link()
    {
        $js = $this->createExtension();

        $script = $js->link('/resources/js/main.js');

        $this->assertContains('<script', $script);
        $this->assertContains('</script>', $script);
        $this->assertContains('src="/resources/js/main.js"', $script);
    }

    /** @test */
    public function test_defer()
    {
        $js = $this->createExtension();

        $script = $js->defer()->link('/resources/js/main.js');

        $this->assertContains('defer', $script);
    }

    /** @test */
    public function test_async()
    {
        $js = $this->createExtension();

        $script = $js->async()->link('/resources/js/main.js');

        $this->assertContains('async', $script);
    }

    private function createExtension(): Js
    {
        return new Js(
            File::path('public')
        );
    }
}

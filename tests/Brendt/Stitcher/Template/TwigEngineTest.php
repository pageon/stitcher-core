<?php

namespace Brendt\Stitcher\Template;

use Brendt\Stitcher\App;
use Brendt\Stitcher\Stitcher;
use Brendt\Stitcher\Template\Twig\TwigEngine;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;

class TwigEngineTest extends TestCase
{
    public function setUp() {
        App::init('./tests/config.twig.yml');
    }

    /**
     * @return TwigEngine
     */
    private function createEngine() {
        return App::get('service.twig');
    }

    private function getFiles() {
        $finder = new Finder();

        return $finder->files()->in('./tests/src/template_twig')->name('index.html');
    }

    public function test_twig_renders_from_path() {
        $engine = $this->createEngine();
        $files = $this->getFiles();

        foreach ($files as $template) {
            $html = $engine->renderTemplate($template);
            $this->assertContains('<html>', $html);
        }
    }

    public function test_twig_css() {
        $engine = $this->createEngine();
        $files = $this->getFiles();

        foreach ($files as $template) {
            $html = $engine->renderTemplate($template);
            $this->assertContains('body {', $html);
        }
    }

    public function test_twig_js() {
        $engine = $this->createEngine();
        $files = $this->getFiles();

        foreach ($files as $template) {
            $html = $engine->renderTemplate($template);
            $this->assertContains('<script>var foo = \'bar\';', $html);
        }
    }

    public function test_twig_js_async() {
        $engine = $this->createEngine();
        $files = $this->getFiles();

        foreach ($files as $template) {
            $html = $engine->renderTemplate($template);
            $this->assertContains('<script src="js/async.js" async></script>', $html);
        }
    }

    public function test_twig_meta() {
        $engine = $this->createEngine();
        $files = $this->getFiles();

        foreach ($files as $template) {
            $html = $engine->renderTemplate($template);
            $this->assertContains('<meta', $html);
            $this->assertContains('<meta name="viewport" content="width=device-width, initial-scale=1">', $html);
        }
    }

    public function test_twig_meta_with_extra_data() {
        $engine = $this->createEngine();
        $files = $this->getFiles();

        foreach ($files as $template) {
            $html = $engine->renderTemplate($template);
            $this->assertContains('<meta name="og:description"', $html);
        }
    }

    public function test_twig_image() {
        $engine = $this->createEngine();
        $files = Finder::create()->files()->in('./tests/src/template_twig')->name('home.html')->getIterator();
        $files->rewind();
        $template = $files->current();

        $html = $engine->renderTemplate($template);
        $this->assertContains('<img src="/img/blue.jpg" srcset="/img/blue-50.jpg 50w', $html);
    }

    public function test_twig_file() {
        $engine = $this->createEngine();
        $files = Finder::create()->files()->in('./tests/src/template_twig')->name('fileTest.html')->getIterator();
        $files->rewind();
        $template = $files->current();

        $html = $engine->renderTemplate($template);
        $this->assertContains('data-file="/img/blue.jpg"', $html);
    }
}

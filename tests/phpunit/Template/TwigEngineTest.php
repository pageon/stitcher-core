<?php

use Brendt\Stitcher\Template\Twig\TwigEngine;
use Brendt\Stitcher\Config;
use Symfony\Component\Finder\Finder;

class TwigEngineTest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        Config::load('./tests', 'config.twig.yml');
    }

    public function tearDown() {
        Config::reset();
    }

    private function createEngine() {
        return new TwigEngine();
    }

    private function getFiles() {
        $finder = new Finder();

        return $finder->files()->in(Config::get('directories.template'))->name('index.html');
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
        }
    }

    public function test_twig_image() {
        $engine = $this->createEngine();
        $files = Finder::create()->files()->in(Config::get('directories.template'))->name('home.html')->getIterator();
        $files->rewind();
        $template = $files->current();

        $html = $engine->renderTemplate($template);
        $this->assertContains('<img src="/img/blue.jpg" srcset="/img/blue.jpg 50w"', $html);
    }

}

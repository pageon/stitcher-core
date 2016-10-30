<?php

use brendt\stitcher\engine\twig\TwigEngine;
use brendt\stitcher\Config;
use Symfony\Component\Finder\Finder;

class TwigEngineTest extends PHPUnit_Framework_TestCase {

    public function __construct() {
        parent::__construct();
    }

    private function createEngine() {
        return new TwigEngine();
    }

    private function getFiles() {
        Config::load('./tests', 'config.twig.yml');

        $finder = new Finder();

        return $finder->files()->in(Config::get('directories.src') . '/template')->name('index.html');
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

    public function test_twig_meta() {
        $engine = $this->createEngine();
        $files = $this->getFiles();

        foreach ($files as $template) {
            $html = $engine->renderTemplate($template);
            $this->assertContains('<meta', $html);
        }
    }

}

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

    public function test_twig_renders_from_path() {
        Config::load('./tests', 'config.twig.yml');

        $engine = $this->createEngine();

        $finder = new Finder();
        $files = $finder->files()->in(Config::get('directories.src') . '/template_twig')->name('home.html');

        foreach ($files as $template) {
            $html = $engine->renderTemplate($template);
            
            $this->assertContains('<html>', $html);
            $this->assertContains('<meta', $html);
            $this->assertContains('<script>var foo = \'bar\';', $html);
            $this->assertContains('body {', $html);
        }
    }

}

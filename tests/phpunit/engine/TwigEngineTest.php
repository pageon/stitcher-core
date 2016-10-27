<?php

use brendt\stitcher\engine\twig\TwigEngine;
use brendt\stitcher\Config;
use Symfony\Component\Finder\Finder;

class TwigEngineTest extends PHPUnit_Framework_TestCase {

    public function __construct() {
        parent::__construct();

        Config::load('./tests');
    }

    private function createEngine() {
        return new TwigEngine();
    }

    public function test_smarty_renders_from_path() {
        $engine = $this->createEngine();

        $finder = new Finder();
        $files = $finder->files()->in(Config::get('directories.src') . '/template')->name('index_twig.html');

        foreach ($files as $template) {
            $html = $engine->renderTemplate($template);
            $this->assertContains('<html>', $html);
        }
    }

}

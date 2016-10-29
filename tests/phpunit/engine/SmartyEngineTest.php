<?php

use brendt\stitcher\engine\smarty\SmartyEngine;
use brendt\stitcher\Config;
use Symfony\Component\Finder\Finder;

class SmartyEngineTest extends PHPUnit_Framework_TestCase {

    public function __construct() {
        parent::__construct();

        Config::load('./tests');
    }

    private function createEngine() {
        return new SmartyEngine();
    }

    public function test_smarty_renders_from_path() {
        Config::load('./tests', 'config.yml');
        $engine = $this->createEngine();

        $finder = new Finder();
        $files = $finder->files()->in(Config::get('directories.src') . '/template')->name('index.tpl');

        foreach ($files as $template) {
            $html = $engine->renderTemplate($template);
            $this->assertContains('<html>', $html);
        }
    }

}

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

    private function getFiles() {
        Config::load('./tests', 'config.yml');

        $finder = new Finder();

        return $finder->files()->in(Config::get('directories.src') . '/template')->name('index.tpl');
    }

    public function test_smarty_renders_from_path() {
        $engine = $this->createEngine();
        $files = $this->getFiles();

        foreach ($files as $template) {
            $html = $engine->renderTemplate($template);
            $this->assertContains('<html>', $html);
        }
    }

    public function test_smarty_css() {
        $engine = $this->createEngine();
        $files = $this->getFiles();

        foreach ($files as $template) {
            $html = $engine->renderTemplate($template);
            $this->assertContains('body {', $html);
        }
    }

    public function test_smarty_js() {
        $engine = $this->createEngine();
        $files = $this->getFiles();

        foreach ($files as $template) {
            $html = $engine->renderTemplate($template);
            $this->assertContains('<script>var foo = \'bar\';', $html);
        }
    }

    public function test_smarty_meta() {
        $engine = $this->createEngine();
        $files = $this->getFiles();

        foreach ($files as $template) {
            $html = $engine->renderTemplate($template);
            $this->assertContains('<meta', $html);
        }
    }

}

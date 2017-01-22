<?php

namespace Brendt\Stitcher\Tests\Phpunit\Template;

use Brendt\Stitcher\Config;
use Brendt\Stitcher\Template\Smarty\SmartyEngine;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;

class SmartyEngineTest extends TestCase
{

    public function setUp() {
        Config::load('./tests', 'config.yml');
    }

    private function createEngine() {
        return new SmartyEngine();
    }

    private function getFiles() {
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

    public function test_smarty_js_async() {
        $engine = $this->createEngine();
        $files = $this->getFiles();

        foreach ($files as $template) {
            $html = $engine->renderTemplate($template);
            $this->assertContains('<script src="js/async.js" async></script>', $html);
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

    public function test_smarty_image() {
        $engine = $this->createEngine();
        $files = Finder::create()->files()->in(Config::get('directories.src') . '/template')->name('home.tpl')->getIterator();
        $files->rewind();
        $template = $files->current();

        $engine->addTemplateVariables([
            'content'  => 'test',
            'churches' => [],
        ]);

        $html = $engine->renderTemplate($template);
        $this->assertContains('<img src="/img/blue.jpg" srcset="/img/blue.jpg 50w"', $html);
    }


}

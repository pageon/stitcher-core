<?php

namespace Brendt\Stitcher\Template;

use Brendt\Stitcher\App;
use Brendt\Stitcher\Stitcher;
use Brendt\Stitcher\Template\Smarty\SmartyEngine;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;

class SmartyEngineTest extends TestCase
{
    public function setUp() {
        App::init('./tests/config.yml');
    }

    /**
     * @return SmartyEngine
     */
    private function createEngine() {
        return App::get('service.smarty');
    }

    private function getFiles() {
        $finder = new Finder();

        return $finder->files()->in('./tests/src/template')->name('index.tpl');
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

    public function test_smarty_meta_with_extra_data() {
        $engine = $this->createEngine();
        $files = $this->getFiles();

        foreach ($files as $template) {
            $html = $engine->renderTemplate($template);
            $this->assertContains('<meta name="og:description"', $html);
        }
    }

    public function test_smarty_image() {
        $engine = $this->createEngine();
        $files = Finder::create()->files()->in('./tests/src/template')->name('home.tpl')->getIterator();
        $files->rewind();
        $template = $files->current();

        $engine->addTemplateVariables([
            'content'  => 'test',
            'churches' => [],
        ]);

        $html = $engine->renderTemplate($template);
        $this->assertContains('<img src="/img/blue.jpg" srcset="/img/blue-50.jpg 50w', $html);
    }

    public function test_smarty_file() {
        $engine = $this->createEngine();
        $files = Finder::create()->files()->in('./tests/src/template')->name('fileTest.tpl')->getIterator();
        $files->rewind();
        $template = $files->current();

        $html = $engine->renderTemplate($template);
        $this->assertContains('data-file="/img/blue.jpg"', $html);
    }

}

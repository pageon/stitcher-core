<?php

namespace Brendt\Stitcher\Tests\Phpunit\Template;

use Brendt\Stitcher\Config;
use Brendt\Stitcher\Template\Smarty\SmartyEngine;
use Brendt\Stitcher\Template\TemplatePlugin;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class TemplatePluginTest extends TestCase
{

    public function setUp() {
        Config::load('./tests', 'config.yml');
    }

    /**
     * @return TemplatePlugin
     */
    private function createEnginePlugin() {
        return new TemplatePlugin();
    }

    /**
     * @return SmartyEngine
     */
    private function createSmarty() {
        return new SmartyEngine();
    }

    public function test_css_normal() {
        $publicDir = Config::get('directories.public');
        $plugin = $this->createEnginePlugin();
        $finder = new Finder();
        $fs = new Filesystem();
        $fs->remove("{$publicDir}/css");

        $result = $plugin->css('css/main.css');

        $this->assertEquals('<link rel="stylesheet" type="text/css" href="css/main.css">', $result);
        $this->assertTrue($fs->exists("{$publicDir}/css/main.css"));

        $files = $finder->files()->in($publicDir)->path('css/main.css');
        foreach ($files as $file) {
            $this->assertContains('body {', $file->getContents());
        }
    }

    public function test_css_inline() {
        $plugin = $this->createEnginePlugin();

        $result = $plugin->css('css/main.css', true);

        $this->assertContains('<style>', $result);
        $this->assertContains('body {', $result);
        $this->assertContains('</style>', $result);
    }

    public function test_sass_normal() {
        $publicDir = Config::get('directories.public');
        $plugin = $this->createEnginePlugin();
        $finder = new Finder();
        $fs = new Filesystem();
        $fs->remove("{$publicDir}/css");

        $result = $plugin->css('css/main.scss');

        $this->assertEquals('<link rel="stylesheet" type="text/css" href="css/main.css">', $result);
        $this->assertTrue($fs->exists("{$publicDir}/css/main.css"));

        $files = $finder->files()->in($publicDir)->path('css/main.css');
        foreach ($files as $file) {
            $this->assertContains('p a {', $file->getContents());
        }
    }

    public function test_sass_inline() {
        $plugin = $this->createEnginePlugin();

        $result = $plugin->css('css/main.scss', true);

        $this->assertContains('<style>', $result);
        $this->assertContains('p a {', $result);
        $this->assertContains('</style>', $result);
    }

    public function test_css_in_template() {
        $engine = $this->createSmarty();

        $engine->addTemplateDir(Config::get('directories.src') . '/template');
        $result = $engine->fetch('index.tpl');

        $this->assertContains('<style>', $result);
        $this->assertContains('body {', $result);
        $this->assertContains('</style>', $result);
    }

    public function test_js_inline() {
        $plugin = $this->createEnginePlugin();

        $result = $plugin->js('js/main.js', true);

        $this->assertContains('<script>', $result);
        $this->assertContains("var foo = 'bar';", $result);
        $this->assertContains('</script>', $result);
    }

    public function test_js_normal() {
        $publicDir = Config::get('directories.public');
        $plugin = $this->createEnginePlugin();
        $finder = new Finder();
        $fs = new Filesystem();
        $fs->remove("{$publicDir}/js");

        $result = $plugin->js('js/main.js');

        $this->assertEquals('<script src="js/main.js"></script>', $result);
        $this->assertTrue($fs->exists("{$publicDir}/js/main.js"));

        $files = $finder->files()->in($publicDir)->path('js/main.js');
        foreach ($files as $file) {
            $this->assertContains("var foo = 'bar';", $file->getContents());
        }
    }

    public function test_js_async() {
        $plugin = $this->createEnginePlugin();

        $result = $plugin->js('js/main.js', false, true);

        $this->assertEquals('<script src="js/main.js" async></script>', $result);
    }

    public function test_js_in_template() {
        $engine = $this->createSmarty();

        $engine->addTemplateDir(Config::get('directories.src') . '/template');
        $result = $engine->fetch('index.tpl');

        $this->assertContains('<script>', $result);
        $this->assertContains("var foo = 'bar';", $result);
        $this->assertContains('</script>', $result);
    }

    public function test_meta() {
        $plugin = $this->createEnginePlugin();

        $result = $plugin->meta();

        $this->assertContains('<meta name="viewport" content="width=device-width, initial-scale=1">', $result);
    }

    public function test_meta_in_template() {
        $engine = $this->createSmarty();

        $engine->addTemplateDir(Config::get('directories.src') . '/template');
        $result = $engine->fetch('index.tpl');

        $this->assertContains('<meta name="viewport" content="width=device-width, initial-scale=1">', $result);
    }

    public function test_image() {
        $plugin = $this->createEnginePlugin();

        $image = $plugin->image('img/blue.jpg');

        $this->assertTrue(isset($image['src']));
        $this->assertTrue(isset($image['srcset']));
        $this->assertTrue(isset($image['sizes']));
    }

    public function test_file() {
        $plugin = $this->createEnginePlugin();

        $path = $plugin->file('img/blue.jpg');

        $publicDir = Config::get('directories.public');
        $fs = new Filesystem();

        $this->assertTrue(strpos($path, '/') === 0);

        $path = trim($path, '/');
        $this->assertTrue($fs->exists("{$publicDir}/{$path}"));
    }

}

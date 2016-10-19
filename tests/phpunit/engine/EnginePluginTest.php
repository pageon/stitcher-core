<?php

use brendt\stitcher\engine\EnginePlugin;
use brendt\stitcher\Config;
use brendt\stitcher\engine\smarty\SmartyEngine;

class EnginePluginTest extends PHPUnit_Framework_TestCase {

    public function __construct() {
        parent::__construct();

        Config::load('./tests');
    }

    /**
     * @return EnginePlugin
     */
    private function createEnginePlugin() {
        return new EnginePlugin();
    }

    private function createSmarty() {
        return new SmartyEngine();
    }

    public function test_css_normal() {
        $plugin = $this->createEnginePlugin();

        $result = $plugin->css('css/main.css');

        $this->assertEquals('<link rel="stylesheet" type="text/css" href="css/main.css">', $result);
    }

    public function test_css_critical() {
        $plugin = $this->createEnginePlugin();

        $result = $plugin->css('css/main.css', true);

        $this->assertContains('<style>', $result);
        $this->assertContains('body {', $result);
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

}

<?php

use brendt\stitcher\provider\FileProvider;
use brendt\stitcher\Config;

class FileProviderTest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        Config::load('./tests', 'config.yml');
    }

    public function createCssProvider() {
        return new FileProvider();
    }

    public function test_parse_css() {
        $provider = $this->createCssProvider();

        $result = $provider->parse('css/main.css');
        
        $this->assertContains('body {', $result);
    }

    public function test_parse_js() {
        $provider = $this->createCssProvider();

        $result = $provider->parse('js/main.js');

        $this->assertContains("var foo = 'bar';", $result);
    }

}

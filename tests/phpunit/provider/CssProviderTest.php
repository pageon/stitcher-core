<?php

use brendt\stitcher\provider\CssProvider;
use brendt\stitcher\Config;

class CssProviderTest extends PHPUnit_Framework_TestCase {

    public function __construct() {
        parent::__construct();

        Config::load('./tests');
    }

    public function createCssProvider() {
        return new CssProvider();
    }

    public function test_parse() {
        $provider = $this->createCssProvider();

        $result = $provider->parse('css/main.css');
        
        $this->assertContains('body {', $result);
    }

}

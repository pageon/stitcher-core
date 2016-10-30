<?php

use brendt\stitcher\provider\SassProvider;
use brendt\stitcher\Config;

class SassProviderTest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        Config::load('./tests', 'config.yml');
    }

    public function createSassProvider() {
        return new SassProvider();
    }

    public function test_parse() {
        $provider = $this->createSassProvider();

        $result = $provider->parse('css/main.scss');

        $this->assertContains('p a {', $result);
    }

}

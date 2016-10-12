<?php

use brendt\stitcher\Config;

class ConfigTest extends PHPUnit_Framework_TestCase {

    public function test_config_load() {
        Config::load('./tests');

        $this->assertNotEmpty(Config::get('directories'));
    }

    public function test_recursive_config_load() {
        Config::load('./tests');

        $this->assertNotEmpty(Config::get('image.dimensions'));
    }

}

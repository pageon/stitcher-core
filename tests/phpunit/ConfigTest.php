<?php

use Brendt\Stitcher\Config;

class ConfigTest extends PHPUnit_Framework_TestCase {

    public function test_config_load() {
        Config::reset();
        Config::load('./tests');

        $this->assertNotEmpty(Config::get('directories'));
    }

    public function test_recursive_config_load() {
        Config::reset();
        Config::load('./tests');

        $this->assertNotEmpty(Config::get('engines.image'));
    }

    public function test_recursive_config_set() {
        $value = 'A';

        Config::load('./tests');
        Config::set('test.property.a', $value);

        $this->assertEquals($value, Config::get('test.property.a'));
    }

}

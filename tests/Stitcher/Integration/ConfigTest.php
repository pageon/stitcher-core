<?php

namespace Stitcher\Test\Integration;

use Pageon\Config;
use Stitcher\Test\StitcherTest;

class ConfigTest extends StitcherTest
{
    protected function setUp()
    {
        parent::setUp();

        Config::init(__DIR__.'/../../resources');
    }

    /** @test */
    public function known_property_returns_value()
    {
        $this->assertEquals('bar', Config::get('nested.item'));
    }

    /** @test */
    public function unknown_property_returns_null()
    {
        $this->assertNull(Config::get('not.known'));
    }

    /** @test */
    public function env_function_used_in_config()
    {
        $this->assertEquals('foo', Config::get('with.env'));
    }

    /** @test */
    public function env_function()
    {
        $this->assertEquals('foo', env('TEST_KEY'));
    }

    /** @test */
    public function config_function()
    {
        $this->assertEquals('test', config('public'));
    }
}

<?php

namespace Stitcher\Test\Integration;

use Pageon\Config;
use Stitcher\Test\StitcherTest;

class ConfigTest extends StitcherTest
{
    protected function setUp()
    {
        parent::setUp();

        Config::init();
    }

    /** @test */
    public function known_property_returns_value(): void
    {
        $this->assertEquals('bar', Config::get('nested.item'));
    }

    /** @test */
    public function nested_properties_can_be_get_as_array(): void
    {
        $this->assertTrue(\is_array(Config::get('nested')));
    }

    /** @test */
    public function unknown_property_returns_null(): void
    {
        $this->assertNull(Config::get('not.known'));
    }

    /** @test */
    public function env_function_used_in_config(): void
    {
        $this->assertEquals('foo', Config::get('with.env'));
    }

    /** @test */
    public function env_function(): void
    {
        $this->assertEquals('foo', env('TEST_KEY'));
    }

    /** @test */
    public function config_function(): void
    {
        $this->assertEquals('test', config('public'));
    }
}

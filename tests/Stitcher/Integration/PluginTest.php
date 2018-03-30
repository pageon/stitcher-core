<?php

namespace Stitcher\Integration;

use Pageon\Config;
use Stitcher\App;
use Stitcher\Test\Plugin\TestPlugin;
use Stitcher\Test\Plugin\TestPluginService;
use Stitcher\Test\StitcherTest;

class PluginTest extends StitcherTest
{
    /** @test */
    public function a_plugin_can_be_registered(): void
    {
        App::init();

        $plugin = App::get(TestPlugin::class);

        $this->assertInstanceOf(TestPlugin::class, $plugin);
    }

    /** @test */
    public function a_plugin_can_register_configuration(): void
    {
        App::init();

        $this->assertEquals(1, Config::get('test.plugin.item'));
    }

    /** @test */
    public function a_plugin_can_register_services(): void
    {
        App::init();

        $testPluginService = App::get(TestPluginService::class);

        $this->assertInstanceOf(TestPluginService::class, $testPluginService);
        $this->assertEquals(1, $testPluginService->item);
    }

    /** @test */
    public function a_plugin_can_be_booted(): void
    {
        App::init();

        /** @var TestPlugin $testPlugin */
        $testPlugin = App::get(TestPlugin::class);

        $this->assertInstanceOf(TestPluginService::class, $testPlugin::$service);
    }
}

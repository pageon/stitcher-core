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
    public function a_plugin_can_be_registered()
    {
        App::init();

        $plugin = App::get(TestPlugin::class);

        $this->assertInstanceOf(TestPlugin::class, $plugin);
    }

    /** @test */
    public function a_plugin_can_register_configuration()
    {
        App::init();

        $this->assertEquals(1, Config::get('test.plugin.item'));
    }

    /** @test */
    public function a_plugin_can_register_services()
    {
        App::init();

        $testPluginService = App::get(TestPluginService::class);

        $this->assertInstanceOf(TestPluginService::class, $testPluginService);
        $this->assertEquals(1, $testPluginService->item);
    }
}

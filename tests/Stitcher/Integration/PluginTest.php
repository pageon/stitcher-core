<?php

namespace Stitcher\Integration;

use Stitcher\App;
use Stitcher\Test\Plugin\TestPlugin;
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
}

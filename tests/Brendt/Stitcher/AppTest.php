<?php

namespace Brendt\Stitcher;

use MyPlugin\MyService;
use PHPUnit\Framework\TestCase;

class AppTest extends TestCase
{
    public function test_plugin_loading() {
        App::init('./tests/plugin.config.yml');

        /** @var MyService $myService */
        $myService = App::get('plugin.my.service');
        $this->assertEquals('test', App::getParameter('plugin.my.parameter'));
        $this->assertEquals($myService->getMyParameter(), App::getParameter('plugin.my.parameter'));
        $this->assertNotNull($myService->getStitcher());
    }
}

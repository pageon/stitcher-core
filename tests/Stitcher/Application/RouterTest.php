<?php

namespace Stitcher\Application;

use GuzzleHttp\Psr7\Request;
use Stitcher\App;
use Stitcher\Test\Controller\MyController;
use Stitcher\Test\StitcherTest;

class RouterTest extends StitcherTest
{
    /** @test */
    public function a_route_can_be_dispatched()
    {
        App::init();

        $router = App::router();

        $router->get('/test/{id}/{name}', MyController::class);

        $response = $router->dispatch(new Request('GET', '/test/1/abc'));

        $this->assertContains('test 1 abc', $response->getBody()->getContents());
    }
}

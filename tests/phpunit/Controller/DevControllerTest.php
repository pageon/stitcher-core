<?php

namespace Brendt\Stitcher\Tests\Phpunit\Controller;

use Brendt\Stitcher\Controller\DevController;
use PHPUnit\Framework\TestCase;

class DevControllerTest extends TestCase
{

    public function test_run() {
        $controller = new DevController('./tests', 'config.yml');
        $response = $controller->run('/');

        $this->assertContains('<html>', $response);
    }

    public function test_run_detail() {
        $controller = new DevController('./tests', 'config.yml');
        $response = $controller->run('/churches/church-a');

        $this->assertContains('<html>', $response);
    }

}

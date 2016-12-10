<?php

namespace brendt\tests\phpunit\controller;

use brendt\stitcher\controller\DevController;

class DevControllerTest extends \PHPUnit_Framework_TestCase {

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

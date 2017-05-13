<?php

namespace Brendt\Stitcher\Command;

use Brendt\Stitcher\App;
use Brendt\Test\CommandTestCase;

class RouterListCommandTest extends CommandTestCase
{
    protected function setUp() {
        App::init('./tests/config.yml');
    }

    public function test_list_command() {
        $output = $this->runCommand('router:list');

        $this->assertContains('/:', $output);
        $this->assertContains('/churches/{id}:', $output);
    }

    public function test_list_command_with_filter() {
        $output = $this->runCommand('router:list /churches');

        $this->assertNotContains('/:', $output);
        $this->assertContains('/churches/{id}:', $output);
    }

}

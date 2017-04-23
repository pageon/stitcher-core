<?php

namespace Brendt\Stitcher\Command;

use Brendt\Test\CommandTestCase;

class RouterDispatchCommandTest extends CommandTestCase
{

    public function test_list_command() {
        $output = $this->runCommand('router:dispatch /churches/12');

        $this->assertContains('/churches/{id}', $output);
    }

}

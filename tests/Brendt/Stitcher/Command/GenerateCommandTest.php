<?php

namespace Brendt\Stitcher\Command;

use Brendt\Test\CommandTestCase;
use Symfony\Component\Filesystem\Filesystem;

class GenerateCommandTest extends CommandTestCase
{

    protected function tearDown() {
        $fs = new Filesystem();

        if ($fs->exists('./tests/public')) {
            $fs->remove('./tests/public');
        }
    }

    public function test_generate_whole_site() {
        $output = $this->runCommand('site:generate');

        $fs = new Filesystem();

        $this->assertTrue($fs->exists('./tests/public/index.html'));
        $this->assertTrue($fs->exists('./tests/public/churches/church-a.html'));
        $this->assertTrue($fs->exists('./tests/public/churches.html'));
        $this->assertTrue($fs->exists('./tests/public/.htaccess'));
        $this->assertContains('success', $output);
    }

    public function test_generate_filtered_page() {
        $this->runCommand('site:generate /');

        $fs = new Filesystem();

        $this->assertTrue($fs->exists('./tests/public/index.html'));
        $this->assertTrue($fs->exists('./tests/public/.htaccess'));
        $this->assertFalse($fs->exists('./tests/public/churches/church-a.html'));
        $this->assertFalse($fs->exists('./tests/public/churches.html'));
    }

}

<?php

namespace Stitcher\Variable;

use Stitcher\File;
use Stitcher\Test\StitcherTest;

class MarkdownVariableTest extends StitcherTest
{
    /** @test */
    public function it_can_be_parsed() {
        $path = File::path('/MarkdownVariableTest_test.md');
        File::write($path, $this->getMarkdown());

        $variable = MarkdownVariable::make($path, new \Parsedown())->parse();

        $this->assertTrue(is_string($variable->getParsed()));
        $this->assertContains('<h1>', $variable->getParsed());
    }

    private function getMarkdown() : string
    {
        return <<<EOT
# Hello world

A simple MD example
EOT;
    }
}

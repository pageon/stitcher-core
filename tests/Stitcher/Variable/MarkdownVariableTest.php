<?php

namespace Stitcher\Variable;

use Stitcher\File;
use Stitcher\Test\CreateStitcherObjects;
use Stitcher\Test\StitcherTest;

class MarkdownVariableTest extends StitcherTest
{
    use CreateStitcherObjects;

    /** @test */
    public function it_can_be_parsed(): void
    {
        $path = File::path('/MarkdownVariableTest_test.md');
        File::write($path, $this->getMarkdown());

        $variable = MarkdownVariable::make($path, $this->createMarkdownParser())->parse();

        $this->assertTrue(\is_string($variable->getParsed()));
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

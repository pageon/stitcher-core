<?php

namespace Stitcher\Variable;

use Stitcher\File;
use Stitcher\Test\StitcherTest;

class JsonVariableTest extends StitcherTest
{
    /** @test */
    public function it_can_be_parsed(): void
    {
        $path = File::path('/JsonVariableTest_test.json');
        File::write($path, json_encode([
            'test' => 'test',
        ]));

        $variable = JsonVariable::make($path)->parse();

        $this->assertTrue(\is_array($variable->getParsed()));
        $this->assertArrayHasKey('test', $variable->getParsed());
    }
}

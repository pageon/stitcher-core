<?php

namespace Stitcher\Test\Stitcher\Variable;

use Stitcher\Test\CreateStitcherObjects;
use Stitcher\Test\StitcherTest;
use Stitcher\Variable\ImageVariable;

class ImageVariableTest extends StitcherTest
{
    use CreateStitcherObjects;

    /** @test */
    public function it_can_be_parsed()
    {
        $variable = ImageVariable::make(
            '/resources/images/green.jpg',
            $this->createImageFactory()
        )->parse();

        $parsed = $variable->getParsed();
        $this->assertArrayHasKey('src', $parsed, '`src` not found in parsed image.');
        $this->assertArrayHasKey('srcset', $parsed, '`srcset not found in parsed image.`');
        $this->assertEquals('/resources/images/green.jpg', $parsed['src'], '`src` does not match expected value in parsed image.');
    }

    /** @test */
    public function it_can_be_parsed_with_alt()
    {
        $variable = ImageVariable::make(
            [
                'src' => '/resources/images/green.jpg',
                'alt' => 'test',
            ],
            $this->createImageFactory()
        )->parse();

        $parsed = $variable->getParsed();
        $this->assertArrayHasKey('alt', $parsed, '`alt not found in parsed image.`');
        $this->assertEquals('test', $parsed['alt'], '`alt` does not match expected value in parsed image.');
        $this->assertEquals('/resources/images/green.jpg', $parsed['src'], '`src` does not match expected value in parsed image.');
    }
}

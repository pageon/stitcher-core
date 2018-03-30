<?php

namespace Pageon\Test\Html\Image;

use Pageon\Html\Image\Image;
use Stitcher\Test\StitcherTest;

class ImageTest extends StitcherTest
{
    /** @test */
    public function it_can_be_made(): void
    {
        $image = Image::make('resources/green.jpg');

        $this->assertInstanceOf(Image::class, $image);
    }

    /** @test */
    public function it_can_be_made_with_sizes(): void
    {
        $image = Image::make('resources/green.jpg', '100vw');

        $this->assertEquals('100vw', $image->sizes());
    }

    /** @test */
    public function it_can_be_made_with_alt(): void
    {
        $image = Image::make('resources/green.jpg', null, 'alt');

        $this->assertEquals('alt', $image->alt());
    }
}

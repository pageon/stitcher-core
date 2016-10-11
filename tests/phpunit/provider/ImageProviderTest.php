<?php

use brendt\stitcher\provider\ImageProvider;

class ImageProviderTest extends PHPUnit_Framework_TestCase {

    protected function createImageProvider() {
        return new ImageProvider('./setup/data', './tests/public');
    }

    public function test_image_create() {
        $imageProvider = $this->createImageProvider();

        $image = $imageProvider->parse('img/green');

        $this->assertNotNull($image['src']);
        $this->assertNotNull($image['srcset']);
    }

}

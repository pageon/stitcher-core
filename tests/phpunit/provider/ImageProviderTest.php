<?php

use brendt\stitcher\provider\ImageProvider;

use Symfony\Component\Filesystem\Filesystem;

class ImageProviderTest extends PHPUnit_Framework_TestCase {

    protected function createImageProvider() {
        return new ImageProvider('./install/data', './tests/public');
    }

    public function test_image_create() {
        $imageProvider = $this->createImageProvider();

        $image = $imageProvider->parse('img/green');

        $this->assertNotNull($image['src']);
        $this->assertNotNull($image['srcset']);
    }

    public function test_image_create_with_extra_fields() {
        $imageProvider = $this->createImageProvider();
        $altText = 'A green image';

        $image = $imageProvider->parse([
            'src' => 'img/green',
            'alt' => $altText,
        ]);

        $this->assertNotNull($image['src']);
        $this->assertNotNull($image['srcset']);
        $this->assertArrayHasKey('alt', $image);
        $this->assertEquals($image['alt'], $altText);
    }

    public function test_image_creates_file() {
        $file = './tests/public/img/green.jpg';
        $fs = new Filesystem();

        if ($fs->exists($file)) {
            $fs->remove($file);
        }

        $imageProvider = $this->createImageProvider();
        $imageProvider->parse('img/green');

        $this->assertTrue($fs->exists($file));
        // TODO: config with 1x1
    }

}

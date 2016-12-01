<?php

use brendt\stitcher\provider\ImageProvider;
use brendt\stitcher\Config;
use Symfony\Component\Filesystem\Filesystem;
use brendt\stitcher\provider\YamlProvider;

class ImageProviderTest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        Config::load('./tests', 'config.yml');
    }

    protected function createImageProvider() {
        return new ImageProvider();
    }

    protected function createYamlProvider() {
        return new YamlProvider();
    }

    public function test_image_create() {
        $imageProvider = $this->createImageProvider();

        $image = $imageProvider->parse('img/green.jpg');

        $this->assertNotNull($image['src']);
        $this->assertNotNull($image['srcset']);
    }

    public function test_image_create_with_extra_fields() {
        $imageProvider = $this->createImageProvider();
        $altText = 'A green image';

        $image = $imageProvider->parse([
            'src' => 'img/green.jpg',
            'alt' => $altText,
        ]);

        $this->assertNotNull($image['src']);
        $this->assertEquals($image['src'], '/img/green.jpg');
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
        $imageProvider->parse('img/green.jpg');

        $this->assertTrue($fs->exists($file));
    }

    public function test_image_parses_with_extended_fields() {
        $provider = $this->createYamlProvider();

        $data = $provider->parse('church-image.yml');

        $this->assertTrue(isset($data['church-image']['image']['src']));
        $this->assertTrue(isset($data['church-image']['image']['srcset']));
        $this->assertTrue(isset($data['church-image']['image']['alt']));
    }

}

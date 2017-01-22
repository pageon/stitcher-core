<?php

namespace Brendt\Stitcher\Tests\Phpunit\Parser;

use Brendt\Stitcher\Config;
use Brendt\Stitcher\Parser\ImageParser;
use Brendt\Stitcher\Parser\YamlParser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class ImageParserTest extends TestCase
{

    public function setUp() {
        Config::load('./tests', 'config.yml');
    }

    protected function createImageParser() {
        return new ImageParser();
    }

    protected function createYamlParser() {
        return new YamlParser();
    }

    public function test_image_create() {
        $imageParser = $this->createImageParser();

        $image = $imageParser->parse('img/green.jpg');

        $this->assertNotNull($image['src']);
        $this->assertNotNull($image['srcset']);
    }

    public function test_image_create_with_extra_fields() {
        $imageParser = $this->createImageParser();
        $altText = 'A green image';

        $image = $imageParser->parse([
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

        $imageParser = $this->createImageParser();
        $imageParser->parse('img/green.jpg');

        $this->assertTrue($fs->exists($file));
    }

    public function test_image_parses_with_extended_fields() {
        $yamlParser = $this->createYamlParser();

        $data = $yamlParser->parse('church-image.yml');

        $this->assertTrue(isset($data['church-image']['image']['src']));
        $this->assertTrue(isset($data['church-image']['image']['srcset']));
        $this->assertTrue(isset($data['church-image']['image']['alt']));
    }

}

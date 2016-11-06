<?php

namespace brendt\stitcher\tests\element;

use brendt\stitcher\Config;
use brendt\stitcher\element\Image;
use \PHPUnit_Framework_TestCase;
use Symfony\Component\Filesystem\Filesystem;

class ImageTest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        Config::load('./tests');
    }

    public function test_image_create() {
        $fs = new Filesystem();
        $fs->remove('./tests/public/img/logo.png');

        $image = new Image('./tests/src/img/logo.png', 'img/logo.png');

        $this->assertTrue($fs->exists('./tests/public/img/logo.png'));
    }

    public function test_image_scale() {
        $fs = new Filesystem();
        $fs->remove('./tests/public/img/logo-500x500.png');

        $image = new Image('./tests/src/img/logo.png', 'img/logo.png');
        $image->scale(500, 500);

        $this->assertTrue($fs->exists('./tests/public/img/logo-500x500.png'));
    }

}

<?php

namespace Brendt\Stitcher\Parser;

use Brendt\Stitcher\App;
use Brendt\Stitcher\Stitcher;
use PHPUnit\Framework\TestCase;

class SassParserTest extends TestCase
{
    public function setUp() {
        App::init('./tests/config.yml');
    }

    public function createSassParser() {
        return App::get('parser.sass');
    }

    public function test_parse() {
        $sassParser = $this->createSassParser();

        $result = $sassParser->parse('css/main.scss');

        $this->assertContains('p a {', $result);
    }

}

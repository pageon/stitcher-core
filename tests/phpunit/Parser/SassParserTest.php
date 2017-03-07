<?php

namespace Brendt\Stitcher\Tests\Phpunit\Parser;

use Brendt\Stitcher\Stitcher;
use PHPUnit\Framework\TestCase;

class SassParserTest extends TestCase
{
    public function setUp() {
        Stitcher::create('./tests/config.yml');
    }

    public function createSassParser() {
        return Stitcher::get('parser.sass');
    }

    public function test_parse() {
        $sassParser = $this->createSassParser();

        $result = $sassParser->parse('css/main.scss');

        $this->assertContains('p a {', $result);
    }

}

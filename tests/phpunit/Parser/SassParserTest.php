<?php

namespace Brendt\Stitcher\Tests\Phpunit\Parser;

use Brendt\Stitcher\Config;
use Brendt\Stitcher\Parser\SassParser;
use PHPUnit\Framework\TestCase;

class SassParserTest extends TestCase
{

    public function setUp() {
        Config::load('./tests', 'config.yml');
    }

    public function createSassParser() {
        return new SassParser();
    }

    public function test_parse() {
        $sassParser = $this->createSassParser();

        $result = $sassParser->parse('css/main.scss');

        $this->assertContains('p a {', $result);
    }

}

<?php

use Brendt\Stitcher\Parser\SassParser;
use Brendt\Stitcher\Config;

class SassParserTest extends PHPUnit_Framework_TestCase {

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

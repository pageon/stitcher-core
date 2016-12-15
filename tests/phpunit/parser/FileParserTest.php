<?php

namespace brendt\tests\phpunit\parser;

use brendt\stitcher\parser\FileParser;
use brendt\stitcher\Config;
use PHPUnit_Framework_TestCase;

class FileParserTest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        Config::load('./tests', 'config.yml');
    }

    public function createParser() {
        return new FileParser();
    }

    public function test_parse_css() {
        $parser = $this->createParser();

        $result = $parser->parse('css/main.css');
        
        $this->assertContains('body {', $result);
    }

    public function test_parse_js() {
        $parser = $this->createParser();

        $result = $parser->parse('js/main.js');

        $this->assertContains("var foo = 'bar';", $result);
    }

}

<?php

namespace Brendt\Stitcher\Tests\Phpunit\Parser;

use Brendt\Stitcher\Parser\FileParser;
use Brendt\Stitcher\Config;
use PHPUnit\Framework\TestCase;

class FileParserTest extends TestCase
{

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

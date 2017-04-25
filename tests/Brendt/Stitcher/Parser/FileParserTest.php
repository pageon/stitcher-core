<?php

namespace Brendt\Stitcher\Parser;

use Brendt\Stitcher\App;
use Brendt\Stitcher\Stitcher;
use PHPUnit\Framework\TestCase;

class FileParserTest extends TestCase
{
    public function setUp() {
        App::init('./tests/config.yml');
    }

    /**
     * @return FileParser
     */
    public function createParser() {
        return App::get('parser.file');
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

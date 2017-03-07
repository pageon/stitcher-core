<?php

namespace Brendt\Stitcher\Tests\Phpunit\Parser;

use Brendt\Stitcher\Parser\FileParser;
use Brendt\Stitcher\Stitcher;
use PHPUnit\Framework\TestCase;

class FileParserTest extends TestCase
{
    /**
     * @return FileParser
     */
    public function createParser() {
        Stitcher::create('./tests/config.yml');

        return Stitcher::get('parser.file');
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

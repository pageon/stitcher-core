<?php

use Brendt\Stitcher\Parser\MarkdownParser;
use Brendt\Stitcher\Config;

class MarkdownParserTest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        Config::load('./tests', 'config.yml');
    }

    protected function createMarkdownParser() {
        return new MarkdownParser('./install/data');
    }

    public function test_markdown_parser_creates_html() {
        $markdownParser = $this->createMarkdownParser();

        $html = $markdownParser->parse('home');

        $this->assertContains('<h1>', $html);
    }

}

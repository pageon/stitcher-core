<?php

namespace Brendt\Stitcher\Parser;

use Brendt\Stitcher\App;
use Brendt\Stitcher\Stitcher;
use PHPUnit\Framework\TestCase;

class MarkdownParserTest extends TestCase
{
    public function setUp() {
        App::init('./tests/config.yml');
    }

    protected function createMarkdownParser() {
                return App::get('parser.markdown');
    }

    public function test_markdown_parser_creates_html() {
        $markdownParser = $this->createMarkdownParser();

        $html = $markdownParser->parse('home');

        $this->assertContains('<h1>', $html);
    }

}

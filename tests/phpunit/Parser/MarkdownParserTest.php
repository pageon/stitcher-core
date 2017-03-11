<?php

namespace Brendt\Stitcher\Tests\Phpunit\Parser;

use Brendt\Stitcher\Stitcher;
use PHPUnit\Framework\TestCase;

class MarkdownParserTest extends TestCase
{
    public function setUp() {
        Stitcher::create('./tests/config.yml');
    }

    protected function createMarkdownParser() {
                return Stitcher::get('parser.markdown');
    }

    public function test_markdown_parser_creates_html() {
        $markdownParser = $this->createMarkdownParser();

        $html = $markdownParser->parse('home');

        $this->assertContains('<h1>', $html);
    }

}

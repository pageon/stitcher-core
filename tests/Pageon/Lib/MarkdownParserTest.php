<?php

namespace Pageon\Test\Html;

use Stitcher\Test\CreateStitcherObjects;
use Stitcher\Test\StitcherTest;

class MarkdownParserTest extends StitcherTest
{
    use CreateStitcherObjects;

    /** @var \Pageon\Lib\Markdown\MarkdownParser */
    private $parser;

    protected function setUp()
    {
        parent::setUp();

        $this->parser = $this->createMarkdownParser();
    }

    /** @test */
    public function target_blank_links()
    {
        $html = $this->parser->parse('[test](*https://stitcher.io)');

        $this->assertContains('target="_blank"', $html);
    }

    /** @test */
    public function images_are_parsed_with_the_image_parser()
    {
        $html = $this->parser->parse('![test](resources/images/green.jpg)');

        $this->assertContains('srcset', $html);
    }
}

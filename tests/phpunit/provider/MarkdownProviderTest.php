<?php

use brendt\stitcher\provider\MarkdownProvider;
use brendt\stitcher\Config;

class MarkdownProviderTest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        Config::load('./tests', 'config.yml');
    }

    protected function createMarkdownProvider() {
        return new MarkdownProvider('./install/data');
    }

    public function test_markdown_provider_creates_html() {
        $provider = $this->createMarkdownProvider();

        $html = $provider->parse('home');

        $this->assertContains('<h1>', $html);
    }

}

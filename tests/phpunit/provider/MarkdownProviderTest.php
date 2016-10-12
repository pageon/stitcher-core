<?php

use brendt\stitcher\provider\MarkdownProvider;

class MarkdownProviderTest extends PHPUnit_Framework_TestCase {

    protected function createMarkdownProvider() {
        return new MarkdownProvider('./install/data');
    }

    public function test_markdown_provider_creates_html() {
        $provider = $this->createMarkdownProvider();

        $html = $provider->parse('home');

        $this->assertContains('<h1>', $html);
    }

}

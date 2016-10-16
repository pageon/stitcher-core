<?php

use brendt\stitcher\provider\MarkdownProvider;
use brendt\stitcher\Config;

class MarkdownProviderTest extends PHPUnit_Framework_TestCase {

    public function __construct() {
        parent::__construct();

        Config::load('./tests');
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

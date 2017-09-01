<?php

namespace Brendt\Stitcher\Lib;

use Brendt\Stitcher\App;
use PHPUnit\Framework\TestCase;

class ParsedownTest extends TestCase
{
    protected function setUp()
    {
        App::init('./tests/config.yml');
    }

    private function createParsedown() : Parsedown
    {
        return App::get('service.parsedown');
    }

    public function test_link_with_target_blank()
    {
        $parser = $this->createParsedown();
        $element = [
            'text'       => 'hello',
            'attributes' => [
                'href' => '*https://www.stitcher.io',
            ],
        ];

        $html = $parser->element($element);

        $this->assertContains('target="_blank"', $html);
        $this->assertContains('rel="noreferrer noopener"', $html);
        $this->assertContains('href="https://www.stitcher.io"', $html);
    }

    public function test_link_without_target_blank()
    {
        $parser = $this->createParsedown();
        $element = [
            'text'       => 'hello',
            'name'       => 'a',
            'attributes' => [
                'href' => 'https://www.stitcher.io',
            ],
        ];

        $html = $parser->element($element);

        $this->assertNotContains('target="_blank"', $html);
        $this->assertContains('href="https://www.stitcher.io"', $html);
    }

    public function test_link_from_markdown()
    {
        $parser = $this->createParsedown();

        $html = $parser->parse('[text](*https://www.stitcher.io)');

        $this->assertContains('>text</a>', $html);
        $this->assertContains('target="_blank"', $html);
        $this->assertContains('rel="noreferrer noopener"', $html);
        $this->assertContains('href="https://www.stitcher.io"', $html);
    }

    public function test_image_with_srcset()
    {
        $parser = $this->createParsedown();
        $element = [
            'attributes' => [
                'src'    => '/img/a.jpg',
                'srcset' => '/img/a-800.jpg 800w, /img/a-500.jpg 500w',
                'sizes'  => '100vw',
                'alt'    => 'alt',
            ],
        ];

        $html = $parser->element($element);

        $this->assertContains('src="/img/a.jpg"', $html);
        $this->assertContains('srcset="/img/a-800.jpg 800w, /img/a-500.jpg 500w"', $html);
        $this->assertContains('sizes="100vw"', $html);
        $this->assertContains('alt="alt"', $html);
    }

    public function test_image_without_srcset()
    {
        $parser = $this->createParsedown();
        $element = [
            'name'       => 'img',
            'attributes' => [
                'src' => '/img/a.jpg',
            ],
        ];

        $html = $parser->element($element);

        $this->assertContains('src="/img/a.jpg"', $html);
        $this->assertNotContains('srcset="', $html);
        $this->assertNotContains('sizes="', $html);
    }

    public function test_image_from_markdown()
    {
        $parser = $this->createParsedown();

        $html = $parser->parse('![alt](/img/blue.jpg)');

        $this->assertContains('src="/img/blue.jpg"', $html);
        $this->assertContains('srcset="', $html);
        $this->assertContains('sizes="', $html);
        $this->assertContains('alt="alt"', $html);
    }
}

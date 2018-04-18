<?php

namespace Pageon\Test\Html\Meta;

use Pageon\Html\Meta\Meta;
use PHPUnit\Framework\TestCase;

class MetaTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_constructed(): void
    {
        $meta = Meta::create();

        $this->assertNotNull($meta);
    }

    public function test_social_meta_title(): void
    {
        $meta = Meta::create();
        $meta->title('test');
        $html = $meta->render();

        $this->assertContains('<meta property="og:title', $html);
        $this->assertContains('<meta name="twitter:title', $html);
        $this->assertContains('<meta name="title', $html);
    }

    public function test_social_meta_description(): void
    {
        $meta = Meta::create();
        $meta->description('test');
        $html = $meta->render();

        $this->assertContains('<meta property="og:description', $html);
        $this->assertContains('<meta name="twitter:description', $html);
        $this->assertContains('<meta name="description', $html);
    }

    public function test_social_meta_image(): void
    {
        $meta = Meta::create();
        $meta->image('test');
        $html = $meta->render();

        $this->assertContains('<meta property="og:image', $html);
        $this->assertContains('<meta name="twitter:image', $html);
        $this->assertContains('<meta name="image', $html);
    }

    /** @test */
    public function title_suffix()
    {
        $meta = Meta::create();

        $meta->title('test');

        $html = $meta->render([
            'title' => [
                'suffix' => 'test'
            ]
        ]);

        $this->assertContains('testtest', $html);
    }
}

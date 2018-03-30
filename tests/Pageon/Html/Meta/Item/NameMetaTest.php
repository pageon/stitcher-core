<?php

namespace Pageon\Test\Html\Meta\Item;

use Pageon\Html\Meta\Item\NameMeta;
use PHPUnit\Framework\TestCase;

class NameMetaTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_created(): void
    {
        $meta = NameMeta::create('title', 'Hello World');

        $this->assertNotNull($meta);
    }

    /**
     * @test
     */
    public function it_can_be_rendered(): void
    {
        $meta = NameMeta::create('title', 'Hello World');
        $tag = $meta->render();

        $this->assertContains('<meta name="title" content="Hello World">', $tag);
    }

    /**
     * @test
     */
    public function it_escapes_special_characters(): void
    {
        $meta = NameMeta::create('title', '<script></script>""');
        $tag = $meta->render();

        $this->assertContains('&quot;', $tag);
        $this->assertContains('&gt;', $tag);
        $this->assertContains('&lt;', $tag);
    }
}

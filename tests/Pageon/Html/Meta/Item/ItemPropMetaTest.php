<?php

namespace Pageon\Test\Html\Meta\Item;

use Pageon\Html\Meta\Item\ItemPropMeta;
use PHPUnit\Framework\TestCase;

class ItemPropMetaTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_created(): void
    {
        $meta = ItemPropMeta::create('title', 'Hello World');

        $this->assertNotNull($meta);
    }

    /**
     * @test
     */
    public function it_can_be_rendered(): void
    {
        $meta = ItemPropMeta::create('title', 'Hello World');
        $tag = $meta->render();

        $this->assertContains('<meta itemprop="title" content="Hello World">', $tag);
    }

    /**
     * @test
     */
    public function it_escapes_special_characters(): void
    {
        $meta = ItemPropMeta::create('title', '<script></script>""');
        $tag = $meta->render();

        $this->assertContains('&quot;', $tag);
        $this->assertContains('&gt;', $tag);
        $this->assertContains('&lt;', $tag);
    }
}

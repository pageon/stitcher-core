<?php

namespace Pageon\Test\Html\Meta\Item;

use Pageon\Html\Meta\Item\PropertyMeta;
use PHPUnit\Framework\TestCase;

class PropertyMetaTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_created() {
        $meta = PropertyMeta::create('title', 'Hello World');

        $this->assertNotNull($meta);
    }

    /**
     * @test
     */
    public function it_can_be_rendered() {
        $meta = PropertyMeta::create('title', 'Hello World');
        $tag = $meta->render();

        $this->assertContains('<meta property="title" content="Hello World">', $tag);
    }

    /**
     * @test
     */
    public function it_escapes_special_characters() {
        $meta = PropertyMeta::create('title', '<script></script>""');
        $tag = $meta->render();

        $this->assertContains('&quot;', $tag);
        $this->assertContains('&gt;', $tag);
        $this->assertContains('&lt;', $tag);
    }
}

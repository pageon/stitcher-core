<?php

namespace Pageon\Test\Html\Meta\Item;

use Pageon\Html\Meta\Item\LinkMeta;
use PHPUnit\Framework\TestCase;

class LinkMetaTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_created() {
        $meta = LinkMeta::create('next', '/?page=3');

        $this->assertNotNull($meta);
    }

    /**
     * @test
     */
    public function it_can_be_rendered() {
        $meta = LinkMeta::create('next', '/?page=3');
        $tag = $meta->render();

        $this->assertContains('<link rel="next" href="/?page=3">', $tag);
    }
}

<?php

namespace Pageon\Test\Html\Meta\Item;

use Pageon\Html\Meta\Item\CharsetMeta;
use PHPUnit\Framework\TestCase;

class CharsetMetaTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_created(): void
    {
        $meta = CharsetMeta::create('UTF-16');

        $this->assertNotNull($meta);
    }

    /**
     * @test
     */
    public function it_can_be_rendered(): void
    {
        $meta = CharsetMeta::create('UTF-16');
        $tag = $meta->render();

        $this->assertContains('<meta charset="UTF-16">', $tag);
    }
}

<?php

namespace Stitcher\Page;

use Stitcher\Test\StitcherTest;

class PageTest extends StitcherTest
{
    /** @test */
    public function it_can_be_created()
    {
        $page = Page::make('/home', 'index.twig');

        $this->assertInstanceOf(Page::class, $page);
    }

    /** @test */
    public function it_sets_default_meta_from_variables()
    {
        $page = Page::make('/home', 'index.twig', [
            'title' => 'title',
            'description' => 'description'
        ]);

        $meta = $page->meta()->render();
        $this->assertContains('<meta name="title" content="title">', $meta);
        $this->assertContains('<meta name="description" content="description">', $meta);
    }

    /** @test */
    public function it_sets_default_meta_from_meta_variables()
    {
        $page = Page::make('/home', 'index.twig', [
            'title' => 'title',
            'description' => 'description',
            'meta' => [
                'title' => 'title2',
                'description' => 'description2',
            ],
        ]);

        $meta = $page->meta()->render();
        $this->assertContains('<meta name="title" content="title2">', $meta);
        $this->assertContains('<meta name="description" content="description2">', $meta);
    }
}

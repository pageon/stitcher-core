<?php

namespace Pageon\Test\Html\Meta\Social;

use Pageon\Html\Meta\Meta;
use Pageon\Html\Meta\Social\GooglePlusMeta;
use Pageon\Html\Meta\SocialMeta;
use PHPUnit\Framework\TestCase;

class GooglePlusMetaTest extends TestCase
{
    /** @var Meta */
    private $meta;

    protected function setUp() {
        $this->meta = new Meta();
    }

    private function createSocialMeta() : SocialMeta {
        return new GooglePlusMeta($this->meta);
    }

    /** @test */
    public function it_can_render_the_title() {
        $social = $this->createSocialMeta();

        $social->title('hello');

        $this->assertContains('<meta itemprop="name" content="hello">', $this->meta->render());
    }

    /** @test */
    public function it_can_render_the_description() {
        $social = $this->createSocialMeta();

        $social->description('hello');

        $this->assertContains('<meta itemprop="description" content="hello">', $this->meta->render());
    }

    /** @test */
    public function it_can_render_the_image() {
        $social = $this->createSocialMeta();

        $social->image('hello');

        $this->assertContains('<meta itemprop="image" content="hello">', $this->meta->render());
    }
}

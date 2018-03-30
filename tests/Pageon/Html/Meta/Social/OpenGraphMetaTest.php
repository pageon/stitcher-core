<?php

namespace Pageon\Test\Html\Meta\Social;

use Pageon\Html\Meta\Meta;
use Pageon\Html\Meta\Social\OpenGraphMeta;
use Pageon\Html\Meta\SocialMeta;
use PHPUnit\Framework\TestCase;

class OpenGraphMetaTest extends TestCase
{

    /** @var Meta */
    private $meta;

    protected function setUp() {
        $this->meta = new Meta();
    }

    private function createSocialMeta() : SocialMeta {
        return new OpenGraphMeta($this->meta);
    }

    /** @test */
    public function it_can_render_the_title(): void
    {
        $social = $this->createSocialMeta();

        $social->title('hello');

        $this->assertContains('<meta property="og:title" content="hello">', $this->meta->render());
        $this->assertContains('<meta property="og:type" content="article">', $this->meta->render());
    }

    /** @test */
    public function it_can_render_the_description(): void
    {
        $social = $this->createSocialMeta();

        $social->description('hello');

        $this->assertContains('<meta property="og:description" content="hello">', $this->meta->render());
    }

    /** @test */
    public function it_can_render_the_image(): void
    {
        $social = $this->createSocialMeta();

        $social->image('hello');

        $this->assertContains('<meta property="og:image" content="hello">', $this->meta->render());
    }
}

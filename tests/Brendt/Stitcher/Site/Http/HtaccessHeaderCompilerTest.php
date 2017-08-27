<?php

namespace Brendt\Stitcher\Site\Http;

use Brendt\Stitcher\Site\Page;
use PHPUnit\Framework\TestCase;

class HtaccessHeaderCompilerTest extends TestCase
{

    /**
     * @var Htaccess
     */
    private $htaccess;

    public function setUp() {
        $this->htaccess = new Htaccess('./tests/public');
    }

    /**
     * @test
     */
    public function it_adds_htaccess_headers() {
        $page = new Page('/blog/overview', ['template' => 'blog/overview']);
        $page->addHeader(Header::link('"</main.css>; rel=preload; as=style"'));
        $compiler = new HtaccessHeaderCompiler($this->htaccess);

        $compiler->compilePage($page);

        $this->assertContains(
'    <filesmatch "^overview\.html$">
        Header add Link "</main.css>; rel=preload; as=style"
    </filesmatch>', $this->htaccess->parse());
    }

}

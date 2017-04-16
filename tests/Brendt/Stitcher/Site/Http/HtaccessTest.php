<?php

namespace Brendt\Stitcher\Site\Http;

use Brendt\Stitcher\Site\Page;
use PHPUnit\Framework\TestCase;

class HtaccessTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_load_the_file() {
        $htaccess = new Htaccess('./tests/.test.htaccess');

        $this->assertNotNull($htaccess);
    }

    /**
     * @test
     */
    public function it_can_create_a_page_block() {
        $htaccess = new Htaccess('./tests/.test.htaccess');
        $page = new Page('/blog/read', ['template' => 'blog/overview']);

        $htaccess->getPageBlock($page);
        $htaccess = $htaccess->parse();

        $this->assertContains('<filesmatch "^\/blog/read.html$">', $htaccess);
    }
}

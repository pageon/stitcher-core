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
        $htaccess = new Htaccess('./tests/src/.htaccess');

        $this->assertNotNull($htaccess);
    }

    /**
     * @test
     */
    public function it_can_create_a_page_block() {
        $htaccess = new Htaccess('./tests/src/.htaccess');

        $page = new Page('/blog/read', ['template' => 'blog/overview']);
        $htaccess->getPageBlock($page);

        $this->assertContains('<filesmatch "^read\.html$">', $htaccess->parse());
    }

    /**
     * @test
     */
    public function it_can_clear_page_blocks() {
        $htaccess = new Htaccess('./tests/src/.htaccess');

        $htaccess->clearPageBlocks();

        $this->assertNotContains('<FilesMatch', $htaccess->parse());
    }
}

<?php

namespace Brendt\Stitcher\Tests\Element;

use Brendt\Stitcher\Config;
use \PHPUnit_Framework_TestCase;
use Brendt\Stitcher\Site\Site;
use Brendt\Stitcher\Site\Page;

class SiteTest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        Config::load('./tests');
    }

    private function createSite() {
        return new Site();
    }

    public function test_construct() {
        new Site();
    }

    public function test_iteration() {
        $site = $this->createSite();
        $pageA = new Page('/a', ['template' => 'a']);
        $pageB = new Page('/b', ['template' => 'b']);

        $site->addPage($pageA);
        $site->addPage($pageB);

        $count = 0;

        foreach ($site as $page) {
            $this->assertInstanceOf(Page::class, $page);
            $count++;
        }

        $this->assertEquals(2, $count);
    }

}

<?php

namespace brendt\stitcher\tests\element;

use brendt\stitcher\Config;
use \PHPUnit_Framework_TestCase;
use brendt\stitcher\site\Site;
use brendt\stitcher\site\Page;

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

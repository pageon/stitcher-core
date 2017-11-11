<?php

namespace Pageon\Test\Html;

use Pageon\Html\SiteMap;
use PHPUnit\Framework\TestCase;

class SiteMapTest extends TestCase
{
    /** @test */
    public function it_can_be_rendered()
    {
        $siteMap = new SiteMap('stitcher.io');

        $siteMap->addPath('/blog');
        $siteMap->addPath('/guide');

        $xml = $siteMap->render();

        $this->assertContains('<loc>stitcher.io/blog</loc>', $xml);
        $this->assertContains('<loc>stitcher.io/guide</loc>', $xml);
        $this->assertContains('<lastmod>', $xml);
    }
}

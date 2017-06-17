<?php

namespace Brendt\Stitcher\Site\Seo;

use PHPUnit\Framework\TestCase;

class SiteMapTest extends TestCase
{
    public function test_is_enabled() {
        $this->assertFalse((new SiteMap(null))->isEnabled());
        $this->assertFalse((new SiteMap(''))->isEnabled());
        $this->assertFalse((new SiteMap('/'))->isEnabled());
        $this->assertTrue((new SiteMap('stitcher.io'))->isEnabled());
    }

    public function test_url_render() {
        $siteMap = new SiteMap('stitcher.io');

        $siteMap->addPath('/blog');
        $siteMap->addPath('/guide');

        $xml = $siteMap->render();

        $this->assertContains('<loc>stitcher.io/blog</loc>', $xml);
        $this->assertContains('<loc>stitcher.io/guide</loc>', $xml);
        $this->assertContains('<lastmod>', $xml);
    }
}

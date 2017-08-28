<?php

namespace Brendt\Stitcher\Parser\Site;

use Brendt\Stitcher\App;
use Brendt\Stitcher\Site\Http\Header;
use Brendt\Stitcher\Site\Http\Htaccess;
use Brendt\Stitcher\Site\Page;
use PHPUnit\Framework\TestCase;

class SiteParserTest extends TestCase
{
    protected function setUp() {
        App::init('./tests/config.yml');
    }

    private function createSiteParser() : SiteParser {
        /** @var SiteParser $parser */
        $parser = App::get('parser.site');

        return $parser;
    }

    public function test_site_loading() {
        $siteParser = $this->createSiteParser();
        $site = $siteParser->loadSite();

        foreach ($site as $page) {
            $this->assertNotNull($page->getId());
        }
    }

    public function test_general_meta() {
        $siteParser = $this->createSiteParser();
        $site = $siteParser->loadSite();

        foreach ($site as $page) {
            $this->assertNotNull($page->getMeta());
            $meta = $page->getMeta()->render();
            $this->assertContains('<meta name="viewport" content="width=device-width, initial-scale=1">', $meta);
        }
    }

    public function test_redirect() {
        $siteParser = $this->createSiteParser();
        $siteParser->loadSite();
        $htaccess = App::get('service.htaccess');

        $parsed = $htaccess->parse();

        $this->assertContains('RedirectMatch 301 ^/redirect_test$ /', $parsed);
    }
}

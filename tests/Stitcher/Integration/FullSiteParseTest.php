<?php

namespace Stitcher\Test\Integration;

use Stitcher\App;
use Stitcher\File;
use Stitcher\Test\StitcherTest;

class FullSiteParseTest extends StitcherTest
{
    /** @test */
    public function parse_test()
    {
        App::init();

        $task = App::get('parse');

        $task->execute();

        $this->assertIndexPageParsed();
        $this->assertOverviewPageParsed();
        $this->assertOverviewPaginatedPageParsed();
        $this->assertDetailPageParsed();
        $this->assertImageParsed();
        $this->assertCss();
        $this->assertJs();
    }

    private function assertIndexPageParsed(): void
    {
        $html = File::read('public/index.html');

        $this->assertNotNull($html);
        $this->assertContains('<meta name="title" content="Hello World">', $html);
    }

    private function assertOverviewPageParsed(): void
    {
        $html = File::read('public/entries.html');

        $this->assertNotNull($html);
        $this->assertContains('<h1>A</h1>', $html);
        $this->assertContains('<h1>B</h1>', $html);
        $this->assertContains('<h1>C</h1>', $html);
    }

    private function assertOverviewPaginatedPageParsed(): void
    {
        $page1 = File::read('public/entries-paginated/page-1.html');
        $this->assertNotNull($page1);
        $this->assertContains('<h1>A</h1>', $page1);
        $this->assertContains('<h1>B</h1>', $page1);
        $this->assertNotContains('<h1>C</h1>', $page1);
        $this->assertNotContains('<a href="/entries-paginated/page-1"', $page1);
        $this->assertContains('<a href="/entries-paginated/page-2"', $page1);
        $this->assertContains('<link rel="next" href="/entries-paginated/page-2"', $page1);

        $page2 = File::read('public/entries-paginated/page-2.html');
        $this->assertNotNull($page2);
        $this->assertContains('<h1>C</h1>', $page2);
        $this->assertContains('<a href="/entries-paginated/page-1"', $page2);
        $this->assertContains('<a href="/entries-paginated/page-3"', $page2);
        $this->assertContains('<link rel="prev" href="/entries-paginated/page-1"', $page2);
        $this->assertContains('<link rel="next" href="/entries-paginated/page-3"', $page2);

        $page3 = File::read('public/entries-paginated/page-2.html');
        $this->assertNotNull($page3);
    }

    private function assertDetailPageParsed(): void
    {
        $detail = File::read('public/entries/a.html');
        $this->assertNotNull($detail);
        $this->assertContains('<h1>A</h1>', $detail);
    }

    private function assertImageParsed()
    {
        $detail = File::read('public/entries/a.html');

        $this->assertContains('<img src="/resources/images/green.jpg"', $detail);
        $this->assertContains('srcset="/resources/images/green.jpg 250w', $detail);
        $this->assertContains('alt="test"', $detail);
    }

    private function assertCss()
    {
        $detail = File::read('public/index.html');

        $this->assertContains('<link rel="stylesheet" href="/resources/css/scss_file.css" />', $detail);
        $this->assertContains('<style>', $detail);
        $this->assertContains('</style>', $detail);
        $this->assertContains('body {', $detail);
    }

    private function assertJs()
    {
        $detail = File::read('public/index.html');

        $this->assertContains('<script src="/resources/js/main.js" defer></script>', $detail);
    }
}

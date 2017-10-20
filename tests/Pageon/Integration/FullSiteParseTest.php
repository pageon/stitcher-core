<?php

namespace Pageon\Test\Integration;

use Pageon\Stitcher\Command\Parse;
use Stitcher\File;
use Stitcher\Test\CreateStitcherObjects;
use Stitcher\Test\CreateStitcherFiles;
use Stitcher\Test\StitcherTest;

class FullSiteParseTest extends StitcherTest
{
    use CreateStitcherFiles;
    use CreateStitcherObjects;

    /** @test */
    public function parse_test()
    {
        $configurationFile = File::path('config/site.yaml');

        $this->createAllTemplates();
        $this->createSiteConfiguration($configurationFile);
        $this->createDataFile();
        $this->createImageFiles();

        $command = Parse::make(
            File::path('public'),
            $configurationFile,
            $this->createPageParser(),
            $this->createPageRenderer()
        );

        $command->execute();

        $this->assertIndexPageParsed();
        $this->assertOverviewPageParsed();
        $this->assertOverviewPaginatedPageParsed();
        $this->assertDetailPageParsed();
        $this->assertImageParsed();
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

        $this->assertContains('<img src="/images/green.jpg"', $detail);
        $this->assertContains('srcset="/images/green.jpg 250w', $detail);
        $this->assertContains('alt="test"', $detail);
    }
}

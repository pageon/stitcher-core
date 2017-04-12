<?php

namespace Brendt\Stitcher;

use Brendt\Stitcher\Site\Page;
use PHPUnit\Framework\TestCase;

class SiteParserTest extends TestCase
{
    private function createSiteParser() : SiteParser {
        $stitcher = Stitcher::create('./tests/config.yml');

        $parser = new SiteParser('./tests/src', './tests/src/template', $stitcher::get('factory.parser'), $stitcher::get('factory.template'), $stitcher::get('factory.adapter'));

        return $parser;
    }

    private function createPage() {
        $page = new Page('/{id}', [
            'template'  => 'home',
            'variables' => [
                'church' => 'churches.yml',
                'intro'  => 'intro.md',
            ],
            'adapters'  => [
                'collection' => [
                    'variable' => 'church',
                    'field'    => 'id',
                ],
            ],
        ]);

        return $page;
    }

    public function test_site_loading() {
        $siteParser = $this->createSiteParser();
        $site = $siteParser->loadSite();

        foreach ($site as $page) {
            $this->assertNotNull($page->getId());
        }
    }

    public function test_template_loading() {
        $siteParser = $this->createSiteParser();
        $site = $siteParser->loadTemplates();

        $this->assertArrayHasKey('index', $site);
        $this->assertArrayHasKey('home', $site);
        $this->assertArrayHasKey('churches/detail', $site);
        $this->assertArrayHasKey('churches/overview', $site);
    }

    public function test_parse_adapters() {
        $siteParser = $this->createSiteParser();
        $page = $this->createPage();

        $pages = $siteParser->parseAdapters($page);

        foreach ($pages as $page) {
            $this->assertTrue($page->isParsedVariable('church'));
            $this->assertFalse($page->isParsedVariable('intro'));
        }
    }

    public function test_parse_multiple_adapters() {
        $siteParser = $this->createSiteParser();
        $page = new Page('/examples', [
            'template'  => 'home',
            'variables' => [
                'entries' => 'combined_entries.yml',
            ],
            'adapters'  => [
                'filter' => [
                    'entries' => [
                        'highlight' => true,
                    ],
                ],
                'order'  => [
                    'variable'  => 'entries',
                    'field'     => 'title',
                    'direction' => '-',
                ],
            ],
        ]);

        $adaptedPages = $siteParser->parseAdapters($page);
        $adaptedPage = reset($adaptedPages);
        $entries = $adaptedPage->getVariable('entries');

        $this->assertCount(4, $entries);
        $this->assertArrayHasKey('entry-a', $entries);
        $this->assertArrayHasKey('entry-b', $entries);
        $this->assertArrayHasKey('entry-e', $entries);
        $this->assertArrayHasKey('entry-g', $entries);
        $this->assertEquals('G', reset($entries)['title']);
        $this->assertEquals('A', end($entries)['title']);
    }

    public function test_parse_variables() {
        $siteParser = $this->createSiteParser();
        $page = $this->createPage();

        $pages = $siteParser->parseAdapters($page);
        $parsedPage = $siteParser->parseVariables($pages['/church-a']);

        $this->assertTrue($parsedPage->isParsedVariable('church'));
        $this->assertTrue($parsedPage->isParsedVariable('intro'));
    }

    public function test_parse_variables_with_normal_array() {
        $siteParser = $this->createSiteParser();
        $page = new Page('/a', [
            'template'  => 'a',
            'variables' => [
                'test' => [
                    'title' => 'title',
                    'body'  => 'body',
                ],
            ],
        ]);

        $parsedPage = $siteParser->parseVariables($page);

        $variable = $parsedPage->getVariable('test');
        $this->assertTrue(isset($variable['title']));
        $this->assertTrue(isset($variable['body']));
    }
}

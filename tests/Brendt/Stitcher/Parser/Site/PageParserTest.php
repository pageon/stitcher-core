<?php

namespace Brendt\Stitcher\Parser\Site;

use Brendt\Stitcher\App;
use Brendt\Stitcher\Site\Http\Header;
use Brendt\Stitcher\Site\Http\Htaccess;
use Brendt\Stitcher\Site\Page;
use PHPUnit\Framework\TestCase;

class PageParserTest extends TestCase
{
    protected function setUp() {
        App::init('./tests/config.yml');
    }

    private function createPageParser() : PageParser {
        /** @var PageParser $parser */
        $parser = App::get('parser.page');

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

    public function test_template_loading() {
        $pageParser = $this->createPageParser();
        $site = $pageParser->loadTemplates();

        $this->assertArrayHasKey('index', $site);
        $this->assertArrayHasKey('home', $site);
        $this->assertArrayHasKey('churches/detail', $site);
        $this->assertArrayHasKey('churches/overview', $site);
    }

    public function test_parse_adapters() {
        $pageParser = $this->createPageParser();
        $page = $this->createPage();

        $pages = $pageParser->parseAdapters($page);

        foreach ($pages as $page) {
            $this->assertTrue($page->isParsedVariable('church'));
            $this->assertFalse($page->isParsedVariable('intro'));
        }
    }

    public function test_parse_multiple_adapters() {
        $pageParser = $this->createPageParser();
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

        $adaptedPages = $pageParser->parseAdapters($page);
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
        $pageParser = $this->createPageParser();
        $page = $this->createPage();

        $pages = $pageParser->parseAdapters($page);
        $parsedPage = $pageParser->parseVariables($pages['/church-a']);

        $this->assertTrue($parsedPage->isParsedVariable('church'));
        $this->assertTrue($parsedPage->isParsedVariable('intro'));
    }

    public function test_parse_variables_with_normal_array() {
        $pageParser = $this->createPageParser();
        $page = new Page('/a', [
            'template'  => 'a',
            'variables' => [
                'test' => [
                    'title' => 'title',
                    'body'  => 'body',
                ],
            ],
        ]);

        $parsedPage = $pageParser->parseVariables($page);

        $variable = $parsedPage->getVariable('test');
        $this->assertTrue(isset($variable['title']));
        $this->assertTrue(isset($variable['body']));
    }

    public function test_meta_compilers() {
        $pageParser = $this->createPageParser();
        $page = new Page('/a', [
            'template'  => 'index',
            'variables' => [
                'title' => 'A',
                'meta'  => [
                    'description' => 'B',
                ],
            ],
        ]);

        $pageParser->parsePage($page);
        $meta = $page->meta->render();

        $this->assertContains('name="title" content="A"', $meta);
        $this->assertContains('name="description" content="B"', $meta);
    }

    public function test_header_compilers() {
        $pageParser = $this->createPageParser();
        $page = new Page('/a', [
            'template' => 'index',
        ]);
        $page->addHeader(Header::link('"</main.css>; rel=preload; as=style"'));

        $pageParser->parsePage($page);
        /** @var Htaccess $htaccess */
        $htaccess = App::get('service.htaccess');

        $this->assertContains(
            '<ifmodule mod_headers.c>
    <filesmatch "^a\.html$">
        Header add Link "</main.css>; rel=preload; as=style"
    </filesmatch>
</ifmodule>', $htaccess->parse());
    }
}

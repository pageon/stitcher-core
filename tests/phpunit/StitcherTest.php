<?php

namespace Brendt\Stitcher\Tests\Phpunit;

use Brendt\Stitcher\Config;
use Brendt\Stitcher\Factory\TemplateEngineFactory;
use Brendt\Stitcher\Site\Page;
use Brendt\Stitcher\Stitcher;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class StitcherTest extends TestCase
{
    /**
     * @return Stitcher
     */
    protected function createStitcher() : Stitcher {
        return Stitcher::create('./tests/config.yml');
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
        $stitcher = $this->createStitcher();
        $site = $stitcher->loadSite();

        foreach ($site as $page) {
            $this->assertNotNull($page->getId());
        }
    }

    public function test_template_loading() {
        $stitcher = $this->createStitcher();
        $site = $stitcher->loadTemplates();

        $this->assertArrayHasKey('index', $site);
        $this->assertArrayHasKey('home', $site);
        $this->assertArrayHasKey('churches/detail', $site);
        $this->assertArrayHasKey('churches/overview', $site);
    }

    public function test_parse_adapters() {
        $stitcher = $this->createStitcher();
        $page = $this->createPage();

        $pages = $stitcher->parseAdapters($page);

        foreach ($pages as $page) {
            $this->assertTrue($page->isParsedVariable('church'));
            $this->assertFalse($page->isParsedVariable('intro'));
        }
    }

    public function test_parse_multiple_adapters() {
        $stitcher = $this->createStitcher();
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

        $adaptedPages = $stitcher->parseAdapters($page);
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
        $stitcher = $this->createStitcher();
        $page = $this->createPage();

        $pages = $stitcher->parseAdapters($page);
        $parsedPage = $stitcher->parseVariables($pages['/church-a']);

        $this->assertTrue($parsedPage->isParsedVariable('church'));
        $this->assertTrue($parsedPage->isParsedVariable('intro'));
    }

    public function test_parse_variables_with_normal_array() {
        $stitcher = $this->createStitcher();
        $page = new Page('/a', [
            'template'  => 'a',
            'variables' => [
                'test' => [
                    'title' => 'title',
                    'body'  => 'body',
                ],
            ],
        ]);

        $parsedPage = $stitcher->parseVariables($page);

        $variable = $parsedPage->getVariable('test');
        $this->assertTrue(isset($variable['title']));
        $this->assertTrue(isset($variable['body']));
    }

    public function test_stitch() {
        $stitcher = $this->createStitcher();
        $blanket = $stitcher->stitch();

        $this->assertArrayHasKey('/', $blanket);
        $this->assertArrayHasKey('/churches', $blanket);
        $this->assertArrayHasKey('/churches/church-a', $blanket);

        foreach ($blanket as $page) {
            $this->assertContains("<html>", $page);
        }

        $this->assertArrayHasKey('/', $blanket);
        $this->assertTrue(strpos($blanket['/'], '<h1>') !== false);
    }

    public function test_stitch_single_route() {
        $stitcher = $this->createStitcher();
        $blanket = $stitcher->stitch('/churches/{id}');

        $this->assertArrayNotHasKey('/', $blanket);
        $this->assertArrayNotHasKey('/churches', $blanket);
        $this->assertArrayHasKey('/churches/church-a', $blanket);
    }

    public function test_stitch_multiple_routes() {
        $stitcher = $this->createStitcher();
        $blanket = $stitcher->stitch([
            '/churches/{id}',
            '/',
        ]);

        $this->assertArrayNotHasKey('/churches', $blanket);
        $this->assertArrayHasKey('/', $blanket);
        $this->assertArrayHasKey('/churches/church-a', $blanket);
    }

    public function test_stitch_detail_route() {
        $stitcher = $this->createStitcher();
        $blanket = $stitcher->stitch('/churches/{id}');

        $this->assertArrayHasKey('/churches/church-a', $blanket);
        $this->assertArrayHasKey('/churches/church-b', $blanket);
        $this->assertContains('Church A', $blanket['/churches/church-a']);
        $this->assertContains('Church B', $blanket['/churches/church-b']);
    }

    public function test_save() {
        $public = Config::get('directories.public');
        $fs = new Filesystem();
        $fs->remove($public);

        $stitcher = $this->createStitcher();
        $blanket = $stitcher->stitch();
        $stitcher->save($blanket);

        $this->assertTrue($fs->exists("{$public}/churches/church-a.html"));
        $this->assertTrue($fs->exists("{$public}/churches/church-b.html"));
        $this->assertTrue($fs->exists("{$public}/churches.html"));
        $this->assertTrue($fs->exists("{$public}/index.html"));

        $finder = new Finder();
        /** @var SplFileInfo[] $files */
        $files = $finder->in("{$public}/churches")->name('church-a.html');

        foreach ($files as $file) {
            $html = $file->getContents();

            $this->assertContains('Church A', $html);
        }
    }

    public function test_stitch_route_single() {
        $stitcher = $this->createStitcher();
        $blanket = $stitcher->stitch('/churches/{id}');

        foreach ($blanket as $page => $html) {
            $this->assertContains('Church', $html);
            $this->assertContains('Intro', $html);
        }
    }

    public function test_stitch_route_multiple() {
        $stitcher = $this->createStitcher();
        $blanket = $stitcher->stitch('/');

        $html = $blanket['/'];

        $this->assertContains('Church A', $html);
        $this->assertContains('Church B', $html);
        $this->assertContains('HOOOOME', $html);
    }

    public function test_stitch_with_twig() {
        $templateEngineId = Config::get('templates');
        Config::set('templates', TemplateEngineFactory::TWIG_ENGINE);

        $stitcher = $this->createStitcher();
        $blanket = $stitcher->stitch('/churches/{id}');

        foreach ($blanket as $page => $html) {
            $this->assertContains('Church', $html);
            $this->assertContains('Intro', $html);
        }

        Config::set('templates', $templateEngineId);
    }

}

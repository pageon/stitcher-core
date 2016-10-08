<?php

use brendt\stitcher\Stitcher;

class StitcherTest extends PHPUnit_Framework_TestCase  {

    /**
     * @var string
     */
    protected $root;

    /**
     * StitcherTest constructor.
     */
    public function __construct() {
        parent::__construct();

        $this->root = './tests/src';
    }

    /**
     * @return Stitcher
     */
    protected function createStitcher() {
        return new Stitcher($this->root);
    }

    public function test_site_loading() {
        $stitcher = $this->createStitcher();
        $site = $stitcher->loadSite();

        $this->assertArrayHasKey('/', $site);
        $this->assertArrayHasKey('/churches', $site);
        $this->assertArrayHasKey('/churches/{id}', $site);
    }

    public function test_template_loading() {
        $stitcher = $this->createStitcher();
        $site = $stitcher->loadTemplates();

        $this->assertArrayHasKey('index', $site);
        $this->assertArrayHasKey('home', $site);
        $this->assertArrayHasKey('churches/detail', $site);
        $this->assertArrayHasKey('churches/overview', $site);
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

}

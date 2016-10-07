<?php

class StitcherTest extends AbstractStitcherTest  {

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
        $this->assertArrayHasKey('/churches/{id}', $blanket);

        foreach ($blanket as $page) {
            $this->assertContains("<html>", $page);
        }
    }

}

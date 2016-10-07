<?php

class StitcherTest extends AbstractStitcherTest  {

    public function test_site_loading() {
        $stitcher = $this->createStitcher();
        $site = $stitcher->loadSite();

        $this->assertArrayHasKey('/', $site);
        $this->assertArrayHasKey('/churches', $site);
        $this->assertArrayHasKey('/churches/{slug}', $site);
    }

    public function test_stitch() {
        $stitcher = $this->createStitcher();
        $blanket = $stitcher->stitch();

        $this->assertArrayHasKey('/', $blanket);
        $this->assertArrayHasKey('/churches', $blanket);
        $this->assertArrayHasKey('/churches/{slug}', $blanket);

        foreach ($blanket as $page) {
            $this->assertContains("<html>", $page);
        }
    }

}

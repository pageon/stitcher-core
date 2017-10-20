<?php

namespace Stitcher\Test\Stitcher\Integration;

use Stitcher\Test\CreateStitcherObjects;
use Stitcher\Test\StitcherTest;
use Symfony\Component\Yaml\Yaml;

class BasicMetaTest extends StitcherTest
{
    use CreateStitcherObjects;

    /** @test */
    public function basic_meta_test()
    {
        $pageParser = $this->createPageParser();

        /** @var \Stitcher\Page\Page $page */
        $page = $pageParser->parse($this->createConfiguration())->first();
        $meta = $page->meta()->render();

        $this->assertContains('<meta charset="UTF-8">', $meta);
        $this->assertContains('<meta name="viewport" content="width=device-width, initial-scale=1">', $meta);
        $this->assertContains('<meta name="title" content="title">', $meta);
        $this->assertContains('<meta property="og:title" content="title">', $meta);
        $this->assertContains('<meta name="description" content="description">', $meta);
        $this->assertContains('<meta property="og:description" content="description">', $meta);
    }

    private function createConfiguration(): array
    {
        return Yaml::parse(<<<EOT
id: test
template: index.twig
variables:
    meta:
        title: title
        description: description
EOT
        );
    }
}

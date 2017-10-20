<?php

namespace Stitcher\Test\Stitcher\Integration;

use Stitcher\Test\CreateStitcherObjects;
use Stitcher\Test\StitcherTest;
use Symfony\Component\Yaml\Yaml;

class CollectionMetaTest extends StitcherTest
{
    use CreateStitcherObjects;

    /** @test */
    public function collection_meta_test()
    {
        $pageParser = $this->createPageParser();

        $pages = $pageParser->parse($this->createConfiguration());

        $metaPage1 = $pages['/a']->meta()->render();
        $metaPage2 = $pages['/b']->meta()->render();

        $this->assertContains('<meta name="title" content="A">', $metaPage1);
        $this->assertContains('<meta name="description" content="A">', $metaPage1);

        $this->assertContains('<meta name="title" content="BB">', $metaPage2);
        $this->assertContains('<meta name="description" content="BB">', $metaPage2);
    }

    private function createConfiguration(): array
    {
        return Yaml::parse(<<<EOT
id: /{id}
template: index.twig
variables:
    meta:
        title: title
        description: description
    entry:
        a:
            title: A
            description: A
        b:
            title: B
            description: B
            meta:
                title: BB
                description: BB
config:
    collection:
        variable: entry
        parameter: id                
EOT
        );
    }
}

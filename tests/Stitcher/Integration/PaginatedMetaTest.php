<?php

namespace Stitcher\Test\Integration;

use Stitcher\Test\CreateStitcherObjects;
use Stitcher\Test\StitcherTest;
use Symfony\Component\Yaml\Yaml;

class PaginatedMetaTest extends StitcherTest
{
    use CreateStitcherObjects;

    /** @test */
    public function paginated_meta_test()
    {
        $pageParser = $this->createPageParser();

        $pages = $pageParser->parse($this->createConfiguration());

        $metaPage1 = $pages->get('test/page-1')->meta()->render();
        $metaPage2 = $pages->get('test/page-2')->meta()->render();
        $metaPage3 = $pages->get('test/page-3')->meta()->render();

        $this->assertContains('next', $metaPage1);
        $this->assertNotContains('prev', $metaPage1);

        $this->assertContains('next', $metaPage2);
        $this->assertContains('prev', $metaPage2);

        $this->assertNotContains('next', $metaPage3);
        $this->assertContains('prev', $metaPage3);
    }

    private function createConfiguration(): array
    {
        return Yaml::parse(<<<EOT
id: test/{page}
template: index.twig
variables:
    entries:
        a:
            name: A
        b:
            name: B
        c:
            name: C  
config:
    pagination:
        variable: entries
        perPage: 1
        parameter: page
EOT
        );
    }
}

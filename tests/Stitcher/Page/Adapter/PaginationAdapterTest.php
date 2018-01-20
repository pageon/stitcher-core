<?php

namespace Stitcher\Page\Adapter;

use Stitcher\File;
use Stitcher\Test\CreateStitcherObjects;
use Stitcher\Test\StitcherTest;

class PaginationAdapterTest extends StitcherTest
{
    use CreateStitcherObjects;

    /** @test */
    public function it_can_transform_a_collection_of_entries_into_multiple_pages()
    {
        $pageConfiguration = $this->createPageConfiguration();

        $adapter = PaginationAdapter::make($pageConfiguration['config']['pagination'], $this->createVariableParser());
        $result = $adapter->transform($pageConfiguration);

        $this->assertCount(3, $result);

        $this->assertCount(1, $result['/page-1']['variables']['entries']);
        $this->assertEquals('A', $result['/page-1']['variables']['entries']['a']['name']);

        $this->assertCount(1, $result['/page-2']['variables']['entries']);
        $this->assertEquals('B', $result['/page-2']['variables']['entries']['b']['name']);

        $this->assertCount(1, $result['/page-3']['variables']['entries']);
        $this->assertEquals('C', $result['/page-3']['variables']['entries']['c']['name']);
    }

    /** @test */
    public function it_sets_the_pagination_variable()
    {
        $pageConfiguration = $this->createPageConfiguration();

        $adapter = PaginationAdapter::make($pageConfiguration['config']['pagination'], $this->createVariableParser());
        $result = $adapter->transform($pageConfiguration);

        $page1 = $result['/page-1'];
        $this->assertTrue(isset($page1['variables']['_pagination']));
        $this->assertEquals(2, $page1['variables']['_pagination']['next']['index']);
        $this->assertEquals('/page-2', $page1['variables']['_pagination']['next']['url']);
        $this->assertNull($page1['variables']['_pagination']['previous']);

        $page2 = $result['/page-2'];
        $this->assertTrue(isset($page2['variables']['_pagination']));
        $this->assertEquals(1, $page2['variables']['_pagination']['previous']['index']);
        $this->assertEquals('/page-1', $page2['variables']['_pagination']['previous']['url']);
        $this->assertEquals(3, $page2['variables']['_pagination']['next']['index']);
        $this->assertEquals('/page-3', $page2['variables']['_pagination']['next']['url']);

        $page3 = $result['/page-3'];
        $this->assertTrue(isset($page3['variables']['_pagination']));
        $this->assertEquals(2, $page3['variables']['_pagination']['previous']['index']);
        $this->assertEquals('/page-2', $page3['variables']['_pagination']['previous']['url']);
        $this->assertNull($page3['variables']['_pagination']['next']);
    }

    private function createPageConfiguration(): array
    {
        File::write('entries.yaml', <<<EOT
a:
    name: A
    category: blog
b:
    name: B
    category: blog
c:
    name: C
    category: news
EOT
        );

        $pageConfiguration = [
            'id'        => '/page-{page}',
            'template'  => 'index.twig',
            'variables' => [
                'entries' => 'entries.yaml',
            ],
            'config'    => [
                'pagination' => [
                    'variable' => 'entries',
                    'perPage'  => 1,
                    'parameter' => 'page',
                ],
            ],
        ];

        return $pageConfiguration;
    }
}

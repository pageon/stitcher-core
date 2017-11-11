<?php

namespace Stitcher\Page\Adapter;

use Stitcher\File;
use Stitcher\Test\CreateStitcherObjects;
use Stitcher\Test\StitcherTest;

class CollectionAdapterTest extends StitcherTest
{
    use CreateStitcherObjects;

    /** @test */
    public function it_can_transform_a_single_collection_into_multiple()
    {
        File::write('entries.yaml', <<<EOT
a:
    name: A
b:
    name: B
EOT
        );

        $pageConfiguration = [
            'id'        => '/{id}',
            'template'  => 'index.twig',
            'variables' => [
                'entry' => 'entries.yaml',
            ],
            'config'    => [
                'collection' => [
                    'variable'  => 'entry',
                    'parameter' => 'id',
                ],
            ],
        ];

        $adapter = CollectionAdapter::make($pageConfiguration['config']['collection'], $this->createVariableParser());
        $result = $adapter->transform($pageConfiguration);

        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey('/a', $result);
        $this->assertEquals('A', $result['/a']['variables']['entry']['name']);
        $this->assertArrayHasKey('/b', $result);
        $this->assertEquals('B', $result['/b']['variables']['entry']['name']);
    }
}

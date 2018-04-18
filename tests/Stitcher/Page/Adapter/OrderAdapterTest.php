<?php

namespace Stitcher\Page\Adapter;

use Stitcher\File;
use Stitcher\Test\CreateStitcherObjects;
use Stitcher\Test\StitcherTest;

class OrderAdapterTest extends StitcherTest
{
    use CreateStitcherObjects;

    /** @test */
    public function it_can_order_values(): void
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
            'id' => '/',
            'template' => 'index.twig',
            'variables' => [
                'entries' => 'entries.yaml',
            ],
            'config' => [
                'order' => [
                    'variable' => 'entries',
                    'field' => 'title',
                    'direction' => 'desc',
                ],
            ],
        ];

        $adapter = OrderAdapter::make($pageConfiguration['config']['order'], $this->createVariableParser());

        $result = $adapter->transform($pageConfiguration);

        $result = reset($result);

        $entries = $result['variables']['entries'];

        $order = array_keys($entries);

        $this->assertEquals(['c', 'b', 'a'], $order);
    }
}

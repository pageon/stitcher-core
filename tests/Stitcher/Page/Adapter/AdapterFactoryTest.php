<?php

namespace Stitcher\Page\Adapter;

use Stitcher\Test\CreateStitcherObjects;
use Stitcher\Test\StitcherTest;

class AdapterFactoryTest extends StitcherTest
{
    use CreateStitcherObjects;

    /** @test */
    public function it_creates_the_correct_adapter(): void
    {
        $factory = AdapterFactory::make($this->createVariableParser());

        $this->assertInstanceOf(CollectionAdapter::class, $factory->create('collection', ['variable' => 'test', 'parameter' => 'id']));
        $this->assertInstanceOf(FilterAdapter::class, $factory->create('filter', ['entries' => ['name' => 'A']]));
        $this->assertInstanceOf(PaginationAdapter::class, $factory->create('pagination', ['variable' => 'entries', 'parameter' => 'page']));
    }
}

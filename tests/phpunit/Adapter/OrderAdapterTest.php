<?php

namespace Brendt\Stitcher\Tests\Phpunit\Adapter;

use Brendt\Stitcher\Adapter\OrderAdapter;
use Brendt\Stitcher\Site\Page;
use Brendt\Stitcher\Stitcher;
use PHPUnit\Framework\TestCase;

class OrderAdapterTest extends TestCase
{
    /**
     * @return OrderAdapter
     */
    private function createAdapter() {
        Stitcher::create('./tests/config.yml');

        return Stitcher::get('adapter.order');
    }

    private function createPage($direction = null) {
        $page = new Page('/entries', [
            'template'  => 'home',
            'variables' => [
                'entries' => 'order_entries.yml',
            ],
            'adapters'  => [
                'order' => [
                    'variable'  => 'entries',
                    'field'     => 'title',
                    'direction' => $direction,
                ],
            ],
        ]);

        return $page;
    }

    public function test_order_adapter_keeps_ids() {
        $page = $this->createPage();
        $adapter = $this->createAdapter();

        $adaptedPages = $adapter->transform($page);
        $adaptedPage = reset($adaptedPages);

        $entries = $adaptedPage->getVariable('entries');

        $this->assertArrayHasKey('entry-a', $entries);
        $this->assertArrayHasKey('entry-b', $entries);
        $this->assertArrayHasKey('entry-c', $entries);
        $this->assertArrayHasKey('entry-d', $entries);
    }

    public function test_order_adapter() {
        $page = $this->createPage();
        $adapter = $this->createAdapter();

        $adaptedPages = $adapter->transform($page);
        $adaptedPage = reset($adaptedPages);

        $entries = $adaptedPage->getVariable('entries');

        $firstEntry = reset($entries);
        $lastEntry = end($entries);

        $this->assertEquals('A', $firstEntry['title']);
        $this->assertEquals('D', $lastEntry['title']);
    }

    public function test_reverse_order_adapter() {
        $page = $this->createPage('-');
        $adapter = $this->createAdapter();

        $adaptedPages = $adapter->transform($page);
        $adaptedPage = reset($adaptedPages);

        $entries = $adaptedPage->getVariable('entries');

        $firstEntry = reset($entries);
        $lastEntry = end($entries);

        $this->assertEquals('D', $firstEntry['title']);
        $this->assertEquals('A', $lastEntry['title']);
    }

    /**
     * @test
     */
    public function it_sorts_multiple_fields() {
        $page = new Page('/entries', [
            'template'  => 'home',
            'variables' => [
                'collectionA' => 'order_entries.yml',
                'collectionB' => 'order_entries.yml',
            ],
            'adapters'  => [
                'order' => [
                    'collectionA' => [
                        'field' => 'title',
                    ],
                    'collectionB' => [
                        'field'     => 'title',
                        'direction' => '-',
                    ],
                ],
            ],
        ]);

        $adapter = $this->createAdapter();
        /** @var Page[] $adaptedPages */
        $adaptedPages = $adapter->transformPage($page);
        $adaptedPage = reset($adaptedPages);

        $collectionA = $adaptedPage->getVariable('collectionA');
        $collectionB = $adaptedPage->getVariable('collectionB');

        $firstEntryA = reset($collectionA);
        $firstEntryB = reset($collectionB);

        $this->assertEquals('A', $firstEntryA['title']);
        $this->assertEquals('D', $firstEntryB['title']);
    }
}

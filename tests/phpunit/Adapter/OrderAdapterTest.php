<?php

namespace Brendt\Stitcher\Tests\Phpunit\Adapter;

use Brendt\Stitcher\Adapter\OrderAdapter;
use Brendt\Stitcher\Config;
use Brendt\Stitcher\Site\Page;
use PHPUnit\Framework\TestCase;

class OrderAdapterTest extends TestCase
{

    public function setUp() {
        Config::load('./tests');
    }

    private function createAdapter() {
        return new OrderAdapter();
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

}

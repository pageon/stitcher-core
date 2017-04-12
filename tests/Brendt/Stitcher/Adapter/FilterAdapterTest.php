<?php

namespace Brendt\Stitcher\Adapter;

use Brendt\Stitcher\Site\Page;
use Brendt\Stitcher\Stitcher;
use PHPUnit\Framework\TestCase;

class FilterAdapterTest extends TestCase
{
    /**
     * @return FilterAdapter
     */
    private function createAdapter() {
        Stitcher::create('./tests/config.yml');

        return Stitcher::get('adapter.filter');
    }

    private function createPage() {
        $page = new Page('/entries', [
            'template'  => 'home',
            'variables' => [
                'entries' => 'filter_entries.yml',
            ],
            'adapters'  => [
                'filter' => [
                    'entries' => [
                        'highlight' => true,
                    ]
                ],
            ],
        ]);

        return $page;
    }

    public function test_filter_adapter() {
        $page = $this->createPage();
        $adapter = $this->createAdapter();

        $adaptedPages = $adapter->transform($page);
        $adaptedPage = reset($adaptedPages);
        
        $entries = $adaptedPage->getVariable('entries');

        $this->assertCount(2, $entries);
    }

}

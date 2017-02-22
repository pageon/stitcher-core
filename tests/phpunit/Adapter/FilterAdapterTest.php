<?php

namespace Brendt\Stitcher\Tests\Phpunit\Adapter;

use Brendt\Stitcher\Adapter\FilterAdapter;
use Brendt\Stitcher\Config;
use Brendt\Stitcher\Site\Page;
use PHPUnit\Framework\TestCase;

class FilterAdapterTest extends TestCase
{

    public function setUp() {
        Config::load('./tests');
    }

    private function createAdapter() {
        return new FilterAdapter();
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

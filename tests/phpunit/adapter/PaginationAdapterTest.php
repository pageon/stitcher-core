<?php

namespace brendt\stitcher\tests\adapter;

use brendt\stitcher\adapter\PaginationAdapter;
use brendt\stitcher\Config;
use brendt\stitcher\element\Page;
use \PHPUnit_Framework_TestCase;

class PaginationAdapterTest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        Config::load('./tests');
    }

    private function createAdapter() {
        return new PaginationAdapter();
    }

    private function createPage() {
        $page = new Page('/entries', [
            'template' => 'home',
            'data'     => [
                'churches' => 'churches.yml',
            ],
            'adapters' => [
                'pagination' => [
                    'variable'    => 'churches',
                    'amount' => 1,
                ],
            ],
        ]);

        return $page;
    }

    public function test_pagination_adapter() {
        $page = $this->createPage();
        $adapter = $this->createAdapter();

        $result = $adapter->transform($page);

        $this->assertArrayHasKey('/entries/page-1', $result);
        $this->assertArrayHasKey('/entries/page-2', $result);
        $this->assertArrayHasKey('/entries', $result);
    }

    public function test_pagination_adapter_results_per_page() {
        $page = $this->createPage();
        $adapter = $this->createAdapter();

        $result = $adapter->transform($page);

        /** @var Page $page */
        foreach ($result as $page) {
            $this->assertCount(1, $page->getVariable('churches'));
        }

        $this->assertArrayHasKey('church-a', $result['/entries/page-1']->getVariable('churches'));
        $this->assertArrayHasKey('church-b', $result['/entries/page-2']->getVariable('churches'));
        $this->assertArrayHasKey('church-a', $result['/entries']->getVariable('churches'));
    }

    public function test_pagination_adapter_sets_pagination_variable() {
        $page = $this->createPage();
        $adapter = $this->createAdapter();

        $result = $adapter->transform($page);

        foreach ($result as $page) {
            $pagination = $page->getVariable('pagination');

            $this->assertArrayHasKey('current', $pagination);
            $this->assertArrayHasKey('next', $pagination);
            $this->assertArrayHasKey('previous', $pagination);
            $this->assertArrayHasKey('pages', $pagination);
        }
    }

    public function test_pagination_adapter_sets_parsed_variables() {
        $page = $this->createPage();
        $adapter = $this->createAdapter();

        $result = $adapter->transform($page);

        foreach ($result as $page) {
            $this->assertTrue($page->isParsedField('churches'));
            $this->assertTrue($page->isParsedField('pagination'));
        }
    }

}

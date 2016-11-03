<?php

namespace brendt\stitcher\tests\adapter;

use brendt\stitcher\adapter\CollectionAdapter;
use brendt\stitcher\Config;
use brendt\stitcher\element\Page;
use \PHPUnit_Framework_TestCase;

class CollectionAdapterTest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        Config::load('./tests');
    }

    private function createAdapter() {
        return new CollectionAdapter();
    }

    private function createPage() {
        $page = new Page('/{id}', [
            'template' => 'home',
            'data' => [
                'church' => 'churches.yml',
            ],
            'adapters' => [
                'collection' => [
                    'name' => 'church',
                    'field' => 'id',
                ]
            ]
        ]);

        return $page;
    }

    public function test_collection_adapter() {
        $page = $this->createPage();
        $adapter = $this->createAdapter();

        $result = $adapter->transform($page);

        $this->assertArrayHasKey('/church-a', $result);
        $this->assertArrayHasKey('/church-b', $result);

        $this->assertEquals('/church-a', $result['/church-a']->getId());
        $this->assertEquals('/church-b', $result['/church-b']->getId());

        $this->assertTrue($result['/church-a']->isParsedField('church'));
        $this->assertTrue($result['/church-b']->isParsedField('church'));
    }

    public function test_collection_adapter_filtered() {
        $page = $this->createPage();
        $adapter = $this->createAdapter();

        $result = $adapter->transform($page, 'church-a');

        $this->assertArrayHasKey('/church-a', $result);
        $this->assertArrayNotHasKey('/church-b', $result);
    }

    /**
     * @expectedException brendt\stitcher\exception\VariableNotFoundException
     */
    public function test_collection_adapter_throws_variable_not_found_exception() {
        $page = new Page('/{id}', [
            'template' => 'home',
            'data' => [
                'church' => 'churches.yml',
            ],
            'adapters' => [
                'collection' => [
                    'name' => 'wrongName',
                    'field' => 'id',
                ]
            ]
        ]);

        $adapter = $this->createAdapter();

        $adapter->transform($page, 'church-a');
    }

    /**
     * @expectedException brendt\stitcher\exception\IdFieldNotFoundException
     */
    public function test_collection_adapter_throws_id_field_not_found_exception() {
        $page = new Page('/{wrongId}', [
            'template' => 'home',
            'data' => [
                'church' => 'churches.yml',
            ],
            'adapters' => [
                'collection' => [
                    'name' => 'church',
                    'field' => 'id',
                ]
            ]
        ]);

        $adapter = $this->createAdapter();

        $adapter->transform($page, 'church-a');
    }

}

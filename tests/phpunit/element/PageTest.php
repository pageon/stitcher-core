<?php

namespace brendt\stitcher\tests\element;

use brendt\stitcher\adapter\CollectionAdapter;
use brendt\stitcher\Config;
use brendt\stitcher\element\Page;
use \PHPUnit_Framework_TestCase;

class PageTest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        Config::load('./tests');
    }

    public function test_construct() {
        $page = new Page('/', [
            'template' => 'home',
        ]);

        $this->assertEquals('/', $page->getId());
    }

    /**
     * @expectedException brendt\stitcher\exception\TemplateNotFoundException
     */
    public function test_construct_throws_template_exception_if_not_set() {
         new Page('/', []);
    }

    public function test_construct_sets_variables() {
        $page = new Page('/', [
            'template' => 'home',
            'data' => [
                'intro' => 'content.md',
            ]
        ]);

        $this->assertArrayHasKey('intro', $page->getVariables());
    }

    public function test_get_variable_exists() {
        $page = new Page('/', [
            'template' => 'home',
            'data' => [
                'intro' => 'content.md',
            ]
        ]);

        $this->assertNotNull($page->getVariable('intro'));
    }

    public function test_get_variable_does_not_exists() {
        $page = new Page('/', [
            'template' => 'home',
            'data' => [
                'intro' => 'content.md',
            ]
        ]);

        $this->assertNull($page->getVariable('intros'));
    }

    public function test_construct_sets_adapters() {
        $page = new Page('/{id}', [
            'template' => 'home',
            'adapters' => [
                'collection' => [
                    'name' => 'church',
                    'field' => 'id',
                ]
            ]
        ]);

        $this->assertArrayHasKey('collection', $page->getAdapters());
    }

    public function test_get_adapter_exists() {
        $page = new Page('/{id}', [
            'template' => 'home',
            'adapters' => [
                'collection' => [
                    'name' => 'church',
                    'field' => 'id',
                ]
            ]
        ]);

        $this->assertNotEmpty($page->getAdapter('collection'));
    }

    public function test_get_adapter_does_not_exist() {
        $page = new Page('/{id}', [
            'template' => 'home',
            'adapters' => [
                'collection' => [
                    'name' => 'church',
                    'field' => 'id',
                ]
            ]
        ]);

        $this->assertNull($page->getAdapter('test'));
    }

    public function test_clear_adapter() {
        $page = new Page('/{id}', [
            'template' => 'home',
            'adapters' => [
                'collection' => [
                    'name' => 'church',
                    'field' => 'id',
                ]
            ]
        ]);

        $page->clearAdapter('collection');
        $this->assertNull($page->getAdapter('collection'));
    }

}

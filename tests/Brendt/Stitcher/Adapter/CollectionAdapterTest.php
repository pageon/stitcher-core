<?php

namespace Brendt\Stitcher\Adapter;

use Brendt\Stitcher\Site\Page;
use Brendt\Stitcher\Stitcher;
use PHPUnit\Framework\TestCase;

class CollectionAdapterTest extends TestCase
{
    /**
     * @return CollectionAdapter
     */
    private function createAdapter() {
        Stitcher::create('./tests/config.yml');

        return Stitcher::get('adapter.collection');
    }

    private function createPage() {
        $page = new Page('/{id}', [
            'template'  => 'home',
            'variables' => [
                'church' => 'churches.yml',
            ],
            'adapters'  => [
                'collection' => [
                    'variable' => 'church',
                    'field'    => 'id',
                ],
            ],
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

        $this->assertTrue($result['/church-a']->isParsedVariable('church'));
        $this->assertTrue($result['/church-b']->isParsedVariable('church'));
    }

    public function test_collection_adapter_filtered() {
        $page = $this->createPage();
        $adapter = $this->createAdapter();

        $result = $adapter->transform($page, 'church-a');

        $this->assertArrayHasKey('/church-a', $result);
        $this->assertArrayNotHasKey('/church-b', $result);
    }

    /**
     * @expectedException \Brendt\Stitcher\Exception\VariableNotFoundException
     */
    public function test_collection_adapter_throws_variable_not_found_exception() {
        $page = new Page('/{id}', [
            'template'  => 'home',
            'variables' => [
                'church' => 'churches.yml',
            ],
            'adapters'  => [
                'collection' => [
                    'variable' => 'wrongName',
                    'field'    => 'id',
                ],
            ],
        ]);

        $adapter = $this->createAdapter();

        $adapter->transform($page, 'church-a');
    }

    /**
     * @expectedException \Brendt\Stitcher\exception\IdFieldNotFoundException
     */
    public function test_collection_adapter_throws_id_field_not_found_exception() {
        $page = new Page('/{wrongId}', [
            'template'  => 'home',
            'variables' => [
                'church' => 'churches.yml',
            ],
            'adapters'  => [
                'collection' => [
                    'variable' => 'church',
                    'field'    => 'id',
                ],
            ],
        ]);

        $adapter = $this->createAdapter();

        $adapter->transform($page, 'church-a');
    }

}

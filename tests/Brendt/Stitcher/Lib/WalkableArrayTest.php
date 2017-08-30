<?php

namespace Brendt\Stitcher\Lib;

use PHPUnit\Framework\TestCase;

class WalkableArrayTest extends TestCase
{
    /** @test */
    public function create_with_array() {
        $array = WalkableArray::fromArray([]);

        $this->assertInstanceOf(WalkableArray::class, $array);
    }

    /** @test */
    public function get_with_array_access() {
        $array = new WalkableArray([
            'country' => [
                'code' => 'BE',
            ],
        ]);

        $this->assertEquals('BE', $array['country.code']);
    }

    /** @test */
    public function set_with_array_access() {
        $array = new WalkableArray();
        $array['country.code'] = 'BE';

        $this->assertEquals('BE', $array['country']['code']);
    }

    /** @test */
    public function simple_get() {
        $array = new WalkableArray([
            'country' => [
                'code' => 'BE',
            ],
        ]);

        $this->assertEquals('BE', $array->get('country.code'));
    }

    /** @test */
    public function simple_set() {
        $array = (new WalkableArray())->set('country.code', 'BE');

        $this->assertTrue(isset($array['country']['code']));
        $this->assertEquals('BE', $array['country']['code']);
    }

    /** @test */
    public function set_with_callable() {
        $array = new WalkableArray();

        $array->set('country.code', function () {
            return 'BE';
        });

        $this->assertEquals('BE', $array['country']['code']);
    }
}

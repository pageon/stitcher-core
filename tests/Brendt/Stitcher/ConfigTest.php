<?php

namespace Brendt\Stitcher;

use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function test_flatten() {
        $config = [
            'a' => [
                'b' => 'hi'
            ],
        ];

        $flatConfig = Config::flatten($config);

        $this->assertTrue(isset($flatConfig['a.b']));
    }
}

<?php

namespace Brendt\Stitcher\Tests\Phpunit\Template;

use Brendt\Stitcher\Config;
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

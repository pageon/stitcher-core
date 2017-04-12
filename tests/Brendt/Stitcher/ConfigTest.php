<?php

namespace Brendt\Stitcher;

use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function test_import_parsing() {
        $config = [
            'imports' => [
                './tests/config.twig.yml',
            ],
            'directories' => [
                'template' => 'my_template'
            ]
        ];
        
        $parsedConfig = Config::parseImports($config);

        $this->assertTrue(isset($parsedConfig['engines']));
        $this->assertTrue(isset($parsedConfig['directories']['template']));
        $this->assertTrue(isset($parsedConfig['directories']['src']));
        $this->assertEquals('my_template', $parsedConfig['directories']['template']);
    }

    public function test_flatten() {
        $config = [
            'a' => [
                'b' => 'hi',
            ],
        ];

        $flatConfig = Config::flatten($config);

        $this->assertTrue(isset($flatConfig['a.b']));
    }
}

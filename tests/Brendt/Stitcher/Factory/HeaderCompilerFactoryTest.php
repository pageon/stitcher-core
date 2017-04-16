<?php

namespace Brendt\Stitcher\Factory;

use Brendt\Stitcher\Stitcher;

class HeaderCompilerFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp() {
        Stitcher::create('./tests/config.yml');
    }

    /**
     * @test
     */
    public function it_finds_the_runtime_compiler() {
        /** @var HeaderCompilerFactory $factory */
        $factory = Stitcher::get('factory.header.compiler');

        $compiler = $factory->setEnvironment('development');
    }
}

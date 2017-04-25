<?php

namespace Brendt\Stitcher\Factory;

use Brendt\Stitcher\App;
use Brendt\Stitcher\Site\Http\HtaccessHeaderCompiler;
use Brendt\Stitcher\Site\Http\RuntimeHeaderCompiler;
use Brendt\Stitcher\Stitcher;
use PHPUnit\Framework\TestCase;

class HeaderCompilerFactoryTest extends TestCase
{
    public function setUp() {
        App::init('./tests/config.yml');
    }

    /**
     * @test
     */
    public function it_finds_the_runtime_compiler() {
        /** @var HeaderCompilerFactory $factory */
        $factory = App::get('factory.header.compiler');

        $compiler = $factory->setEnvironment('development');

        $this->assertInstanceOf(RuntimeHeaderCompiler::class, $compiler->getHeaderCompilerByEnvironment());
    }

    /**
     * @test
     */
    public function it_finds_the_htaccess_compiler() {
        /** @var HeaderCompilerFactory $factory */
        $factory = App::get('factory.header.compiler');

        $compiler = $factory->setEnvironment('production');

        $this->assertInstanceOf(HtaccessHeaderCompiler::class, $compiler->getHeaderCompilerByEnvironment());
    }
}

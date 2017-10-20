<?php

namespace Stitcher\Page;

use Stitcher\Exception\InvalidConfiguration;
use Stitcher\Test\StitcherTest;
use Stitcher\Variable\VariableFactory;
use Stitcher\Variable\VariableParser;

class PageFactoryTest extends StitcherTest
{
    /** @test */
    public function it_can_create_a_page()
    {
        $factory = new PageFactory(
            VariableParser::make(
                VariableFactory::make()
            )
        );

        $page = $factory->create([
            'id'       => '/',
            'template' => 'index.twig',
        ]);

        $this->assertInstanceOf(Page::class, $page);
    }

    /** @test */
    public function it_throws_an_exception_when_id_is_missing()
    {
        $this->expectException(InvalidConfiguration::class);

        $factory = new PageFactory(
            VariableParser::make(
                VariableFactory::make()
            )
        );

        $factory->create([
            'template' => 'index.twig',
        ]);
    }

    /** @test */
    public function it_throws_an_exception_when_template_is_missing()
    {
        $this->expectException(InvalidConfiguration::class);

        $factory = new PageFactory(
            VariableParser::make(
                VariableFactory::make()
            )
        );

        $factory->create([
            'id' => '/',
        ]);
    }
}

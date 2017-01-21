<?php

namespace brendt\stitcher\parser;

class DefaultParserTest extends \PHPUnit_Framework_TestCase {

    /**
     * @test
     */
    public function it_can_parse_a_value_and_return_that_value() {
        $parser = new DefaultParser();
        $path = 'my_test';

        $this->assertEquals($path, $parser->parse($path));
    }

}

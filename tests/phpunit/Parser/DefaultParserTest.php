<?php

namespace Brendt\Stitcher\Tests\Parser;

use Brendt\Stitcher\Parser\DefaultParser;

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

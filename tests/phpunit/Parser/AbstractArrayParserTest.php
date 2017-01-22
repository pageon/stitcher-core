<?php

namespace Brendt\Stitcher\Tests\Parser;

use Brendt\Stitcher\Parser\AbstractArrayParser;
use Brendt\Stitcher\Config;

class AbstractArrayParserTest extends \PHPUnit_Framework_TestCase {

    public function setUp() {
        Config::load('./tests', 'config.yml');
    }

    public function test_parse_normal_array() {
        $parser = new ArrayParserMock();
        $result = $parser->parseEntryData('test', [
            'test' => [
                'title' => 'title',
                'body'  => 'body',
            ],
        ]);

        $this->assertTrue(isset($result['test']['title']));
        $this->assertTrue(isset($result['test']['body']));
    }

}

class ArrayParserMock extends AbstractArrayParser {

    public function parse($path) {
        return;
    }

    public function parseEntryData($id, $entry) {
        return parent::parseEntryData($id, $entry);
    }

}

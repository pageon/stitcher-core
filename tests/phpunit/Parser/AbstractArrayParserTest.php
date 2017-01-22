<?php

namespace Brendt\Stitcher\Tests\Phpunit\Parser;

use Brendt\Stitcher\Parser\AbstractArrayParser;
use Brendt\Stitcher\Config;
use PHPUnit\Framework\TestCase;

class AbstractArrayParserTest extends TestCase
{

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

class ArrayParserMock extends AbstractArrayParser
{

    public function parse($path) {
        return;
    }

    public function parseEntryData($id, $entry) {
        return parent::parseEntryData($id, $entry);
    }

}

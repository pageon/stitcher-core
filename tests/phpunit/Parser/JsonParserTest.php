<?php

namespace Brendt\Stitcher\Tests\Phpunit\Parser;

use Brendt\Stitcher\Exception\ParserException;
use Brendt\Stitcher\Stitcher;
use PHPUnit\Framework\TestCase;

class JsonParserTest extends TestCase
{
    public function setUp() {
        Stitcher::create('./tests/config.yml');
    }

    protected function createJsonParser() {
        return Stitcher::get('parser.json');
    }

    public function test_json_parser_parse_without_extension() {
        $jsonParser = $this->createJsonParser();

        $data = $jsonParser->parse('churches');

        $this->assertNotEmpty($data);
    }

    public function test_json_parser_parse_with_extension() {
        $jsonParser = $this->createJsonParser();

        $data = $jsonParser->parse('churches.json');

        $this->assertNotEmpty($data);
    }

    public function test_json_parser_sets_id_when_parsing() {
        $jsonParser = $this->createJsonParser();

        $data = $jsonParser->parse('churches');

        foreach ($data as $entry) {
            $this->assertArrayHasKey('id', $entry);
        }
    }

    public function test_json_parser_doenst_parse_subfolders() {
        $jsonParser = $this->createJsonParser();

        $data = $jsonParser->parse('churches');

        $this->assertArrayNotHasKey('churches-a', $data);
    }

    public function test_json_parser_parses_single() {
        $jsonParser = $this->createJsonParser();

        $data = $jsonParser->parse('churches/church-a');

        foreach ($data as $entry) {
            $this->assertArrayHasKey('id', $entry);
            $this->assertArrayHasKey('name', $entry);
        }
    }

    public function test_json_parser_parses_recursive() {
        $jsonParser = $this->createJsonParser();

        $data = $jsonParser->parse('churches/church-b');

        foreach ($data as $entry) {
            $this->assertArrayHasKey('body', $entry);
            $this->assertContains('<h2>', $entry['body']);
        }
    }

    public function test_json_parser_exception_handling() {
        $jsonParser = $this->createJsonParser();
        $this->expectException(ParserException::class);

        $jsonParser->parse('error/churches-error');
    }

}

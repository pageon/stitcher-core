<?php

use brendt\stitcher\parser\YamlParser;
use brendt\stitcher\exception\ParserException;
use brendt\stitcher\Config;

class YamlParserTest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        Config::load('./tests', 'config.yml');
    }

    protected function createYamlParser() {
        return new YamlParser();
    }

    public function test_yaml_parser_parse_without_extension() {
        $yamlParser = $this->createYamlParser();

        $data = $yamlParser->parse('churches');

        $this->assertNotEmpty($data);
    }

    public function test_yaml_parser_parse_with_extension() {
        $yamlParser = $this->createYamlParser();

        $data = $yamlParser->parse('churches.yml');

        $this->assertNotEmpty($data);
    }

    public function test_yaml_parser_sets_id_when_parsing() {
        $yamlParser = $this->createYamlParser();

        $data = $yamlParser->parse('churches.yml');

        foreach ($data as $entry) {
            $this->assertArrayHasKey('id', $entry);
        }
    }

    public function test_yaml_parser_parses_single() {
        $yamlParser = $this->createYamlParser();

        $data = $yamlParser->parse('churches/church-c');

        foreach ($data as $entry) {
            $this->assertArrayHasKey('name', $entry);
            $this->assertArrayHasKey('id', $entry);
        }
    }

    public function test_yaml_parser_parses_recursive() {
        $yamlParser = $this->createYamlParser();

        $data = $yamlParser->parse('churches/church-c');

        foreach ($data as $entry) {
            $this->assertArrayHasKey('body', $entry);
            $this->assertContains('<h2>', $entry['body']);
        }
    }

    public function test_yaml_parser_exception_handling() {
        $yamlParser = $this->createYamlParser();
        $this->expectException(ParserException::class);

        $yamlParser->parse('error/churches-error');
    }
}

<?php

use brendt\stitcher\factory\ParserFactory;
use brendt\stitcher\parser\FolderParser;
use brendt\stitcher\parser\JsonParser;
use brendt\stitcher\parser\MarkdownParser;
use brendt\stitcher\parser\YamlParser;
use brendt\stitcher\Config;

class ParserFactoryTest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        Config::load('./tests', 'config.yml');
    }

    protected function createParserFactory() {
        return new ParserFactory();
    }

    public function test_parser_factory_folder() {
        $factory = $this->createParserFactory();

        $this->assertInstanceOf(FolderParser::class, $factory->getParser('churches/'));
        $this->assertInstanceOf(FolderParser::class, $factory->getByType(ParserFactory::FOLDER_PARSER));
    }

    public function test_parser_factory_json() {
        $factory = $this->createParserFactory();

        $this->assertInstanceOf(JsonParser::class, $factory->getParser('churches.json'));
        $this->assertInstanceOf(JsonParser::class, $factory->getByType(ParserFactory::JSON_PARSER));
    }

    public function test_parser_factory_yaml() {
        $factory = $this->createParserFactory();

        $this->assertInstanceOf(YamlParser::class, $factory->getParser('churches.yml'));
        $this->assertInstanceOf(YamlParser::class, $factory->getByType(ParserFactory::YAML_PARSER));
    }

    public function test_parser_factory_markdown() {
        $factory = $this->createParserFactory();

        $this->assertInstanceOf(MarkdownParser::class, $factory->getParser('churches.md'));
        $this->assertInstanceOf(MarkdownParser::class, $factory->getByType(ParserFactory::MARKDOWN_PARSER));
    }

    public function test_get_parser_returns_null_when_no_string_provided() {
        $factory = $this->createParserFactory();

        $this->assertNull($factory->getParser([]));
        $this->assertNull($factory->getParser(23));
    }

}

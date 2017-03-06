<?php

namespace Brendt\Stitcher\Tests\Phpunit\Factory;

use Brendt\Stitcher\Factory\ParserFactory;
use Brendt\Stitcher\Parser\FolderParser;
use Brendt\Stitcher\Parser\JsonParser;
use Brendt\Stitcher\Parser\MarkdownParser;
use Brendt\Stitcher\Parser\YamlParser;
use Brendt\Stitcher\Config;
use PHPUnit\Framework\TestCase;

class ParserFactoryTest extends TestCase
{

    public function setUp() {
        Config::load('./tests', 'config.yml');
    }

    protected function createParserFactory() {
        return new ParserFactory();
    }

    public function test_parser_factory_folder() {
        $factory = $this->createParserFactory();

        $this->assertInstanceOf(FolderParser::class, $factory->getByFileName('churches/'));
        $this->assertInstanceOf(FolderParser::class, $factory->getByType(ParserFactory::EXTENSION_FOLDER));
    }

    public function test_parser_factory_json() {
        $factory = $this->createParserFactory();

        $this->assertInstanceOf(JsonParser::class, $factory->getByFileName('churches.json'));
        $this->assertInstanceOf(JsonParser::class, $factory->getByType(ParserFactory::EXTENSION_JSON));
    }

    public function test_parser_factory_yaml() {
        $factory = $this->createParserFactory();

        $this->assertInstanceOf(YamlParser::class, $factory->getByFileName('churches.yml'));
        $this->assertInstanceOf(YamlParser::class, $factory->getByType(ParserFactory::EXTENSION_YML));
    }

    public function test_parser_factory_markdown() {
        $factory = $this->createParserFactory();

        $this->assertInstanceOf(MarkdownParser::class, $factory->getByFileName('churches.md'));
        $this->assertInstanceOf(MarkdownParser::class, $factory->getByType(ParserFactory::EXTENSION_MD));
    }

    public function test_get_parser_returns_null_when_no_string_provided() {
        $factory = $this->createParserFactory();

        $this->assertNull($factory->getByFileName([]));
        $this->assertNull($factory->getByFileName(23));
    }

}

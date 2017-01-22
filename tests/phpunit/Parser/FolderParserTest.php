<?php

namespace Brendt\Stitcher\Tests\Phpunit\Parser;

use Brendt\Stitcher\Parser\FolderParser;
use Brendt\Stitcher\Config;
use PHPUnit\Framework\TestCase;

class FolderParserTest extends TestCase
{

    public function setUp() {
        Config::load('./tests', 'config.yml');
    }

    protected function createFolderParser() {
        return new FolderParser('./setup/data');
    }

    public function test_folder_parser_parse() {
        $folderParser = $this->createFolderParser();

        $data = $folderParser->parse('churches/');

        $this->assertArrayHasKey('church-a', $data);
        $this->assertArrayHasKey('church-b', $data);
        $this->assertArrayHasKey('church-c', $data);

        $this->assertTrue(isset($data['church-a']['id']));
        $this->assertTrue(isset($data['church-a']['content']));
    }

}

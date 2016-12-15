<?php

use brendt\stitcher\parser\FolderParser;
use brendt\stitcher\Config;

class FolderParserTest extends PHPUnit_Framework_TestCase {

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
    }

}

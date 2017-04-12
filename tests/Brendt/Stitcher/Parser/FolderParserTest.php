<?php

namespace Brendt\Stitcher\Parser;

use Brendt\Stitcher\Stitcher;
use PHPUnit\Framework\TestCase;

class FolderParserTest extends TestCase
{
    public function setUp() {
        Stitcher::create('./tests/config.yml');
    }
    /**
     * @return FolderParser
     */
    protected function createFolderParser() {
        return Stitcher::get('parser.folder');
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

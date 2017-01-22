<?php

namespace Brendt\Stitcher\Parser;

use Brendt\Stitcher\Config;
use Brendt\Stitcher\Factory\ParserFactory;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * The FolderParser take the path to a folder and will read all files in that folder, parsing each of the files
 * individually.
 */
class FolderParser implements Parser
{

    /**
     * @var ParserFactory
     */
    private $parserFactory;

    /**
     * FolderParser constructor
     */
    public function __construct() {
        $this->parserFactory = Config::getDependency('factory.parser');
    }

    /**
     * @param $path
     *
     * @return array
     */
    public function parse($path) {
        $path = trim($path, '/');
        $root = Config::get('directories.src');
        $files = Finder::create()->files()->in("{$root}/data/{$path}")->name('*.*')->sort(function(SplFileInfo $a, SplFileInfo $b) {
            return strcmp($b->getRelativePath(), $a->getRelativePath());
        });
        $data = [];

        foreach ($files as $file) {
            $parser = $this->parserFactory->getParser($file->getFilename());

            $id = str_replace(".{$file->getExtension()}", '', $file->getFilename());

            $data[$id] = [
                'id'      => $id,
                'content' => $parser->parse($file->getRelativePathname()),
            ];
        }

        return $data;
    }
}

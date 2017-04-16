<?php

namespace Brendt\Stitcher\Parser;

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
     * @var string
     */
    private $srcDir;

    /**
     * FolderParser constructor
     *
     * @param ParserFactory $parserFactory
     * @param string        $srcDir
     */
    public function __construct(ParserFactory $parserFactory, string $srcDir) {
        $this->parserFactory = $parserFactory;
        $this->srcDir = $srcDir;
    }

    /**
     * @param $path
     *
     * @return array
     */
    public function parse($path) {
        $path = trim($path, '/');
        /** @var SplFileInfo[] $files */
        $files = Finder::create()->files()->in("{$this->srcDir}/data/{$path}")->name('*.*');
        $data = [];

        foreach ($files as $file) {
            $parser = $this->parserFactory->getByFileName($file->getFilename());
            $id = str_replace(".{$file->getExtension()}", '', $file->getFilename());

            $data[$id] = [
                'id'      => $id,
                'content' => $parser->parse($file->getRelativePathname()),
            ];
        }

        return $data;
    }
}

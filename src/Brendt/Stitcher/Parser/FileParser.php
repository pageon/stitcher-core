<?php

namespace Brendt\Stitcher\Parser;

use Symfony\Component\Finder\Finder;

/**
 * The FileParser takes a path and reads the file contents from that path.
 */
class FileParser implements Parser
{

    /**
     * @var string
     */
    private $srcDir;

    /**
     * FileParser constructor.
     *
     * @param string $srcDir
     */
    public function __construct($srcDir) {
        $this->srcDir = $srcDir;
    }

    /**
     * @param $path
     *
     * @return string
     */
    public function parse($path) {
        $files = Finder::create()->files()->in($this->srcDir)->path(trim($path, '/'))->getIterator();
        $files->rewind();
        $file = $files->current();

        if (!$file) {
            return '';
        }

        return $file->getContents();
    }

}

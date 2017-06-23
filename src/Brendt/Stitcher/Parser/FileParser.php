<?php

namespace Brendt\Stitcher\Parser;

use Symfony\Component\Finder\Finder;

/**
 * The FileParser takes a path and reads the file contents from that path.
 */
class FileParser implements Parser
{
    private $srcDir;

    public function __construct($srcDir) {
        $this->srcDir = $srcDir;
    }

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

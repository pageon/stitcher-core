<?php

namespace brendt\stitcher\parser;

use brendt\stitcher\Config;
use Symfony\Component\Finder\Finder;

/**
 * The FileParser takes a path and reads the file contents from that path.
 */
class FileParser implements Parser {

    /**
     * @param $path
     *
     * @return string
     */
    public function parse($path) {
        $files = Finder::create()->files()->in(Config::get('directories.src'))->path(trim($path, '/'))->getIterator();
        $files->rewind();
        $file = $files->current();

        if (!$file) {
            return '';
        }

        return $file->getContents();
    }

}

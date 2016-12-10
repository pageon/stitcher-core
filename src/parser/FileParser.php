<?php

namespace brendt\stitcher\parser;

use brendt\stitcher\Config;
use Symfony\Component\Finder\Finder;

class FileParser implements Parser {

    /**
     * @param $path
     *
     * @return string
     */
    public function parse($path) {
        $finder = new Finder();
        $files = $finder->files()->in(Config::get('directories.src'))->path(trim($path, '/'));
        $data = '';

        foreach ($files as $file) {
            $data .= $file->getContents();
        }

        return $data;
    }

}

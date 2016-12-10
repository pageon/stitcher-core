<?php

namespace brendt\stitcher\parser;

use brendt\stitcher\factory\ParserFactory;
use Symfony\Component\Finder\Finder;

class FolderParser extends AbstractParser {

    /**
     * @param $path
     *
     * @return array
     */
    public function parse($path) {
        $data = [];
        $path = trim($path, '/');
        $finder = new Finder();
        $files = $finder->files()->in("{$this->root}/data/{$path}")->name('*.*');
        $factory = new ParserFactory();

        foreach ($files as $file) {
            $parser = $factory->getParser($file->getFilename());

            $id = str_replace(".{$file->getExtension()}", '', $file->getFilename());

            $data[$id] = $parser->parse($file->getRelativePathname());
        }

        return $data;
    }
}

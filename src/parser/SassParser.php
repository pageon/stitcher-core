<?php

namespace brendt\stitcher\parser;

use brendt\stitcher\Config;
use Leafo\ScssPhp\Compiler;
use Symfony\Component\Finder\Finder;

/**
 * The SassParser take a path to one or more sass files, compiles it to CSS and returns that CSS.
 */
class SassParser implements Parser {

    /**
     * @param $path
     *
     * @return string
     */
    public function parse($path) {
        /** @var Compiler $sass */
        $sass = Config::getDependency('engine.sass');
        $files = Finder::create()->files()->in(Config::get('directories.src'))->path(trim($path, '/'));
        $data = '';

        foreach ($files as $file) {
            $data .= $sass->compile($file->getContents());
        }

        return $data;
    }

}

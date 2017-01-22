<?php

namespace Brendt\Stitcher\Parser;

use Brendt\Stitcher\Config;
use Parsedown;
use Symfony\Component\Finder\Finder;

/**
 * The MarkDownParser takes a path to a markdown file and will parse it to HTML.
 */
class MarkdownParser implements Parser {

    /**
     * @param string $path
     *
     * @return string
     */
    public function parse($path) {
        if (!strpos($path, '.md')) {
            $path .= '.md';
        }

        $root = Config::get('directories.src');
        $files = Finder::create()->files()->in($root)->path($path)->getIterator();
        $files->rewind();
        $markdownFile = $files->current();

        if (!$markdownFile) {
            return '';
        }

        $html = Parsedown::instance()->parse($markdownFile->getContents());

        return $html;
    }

}

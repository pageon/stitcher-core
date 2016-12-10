<?php

namespace brendt\stitcher\parser;

use Parsedown;
use Symfony\Component\Finder\Finder;

class MarkdownParser extends AbstractParser {

    /**
     * @param string $path
     *
     * @return string
     */
    public function parse($path = '*.md') {
        $html = '';
        $finder = new Finder();
        if (!strpos($path, '.md')) {
            $path .= '.md';
        }
        $files = $finder->files()->in("{$this->root}")->path($path);

        foreach ($files as $file) {
            $html = Parsedown::instance()->parse($file->getContents());

            break;
        }

        return $html;
    }

}

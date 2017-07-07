<?php

namespace Brendt\Stitcher\Parser;

use Brendt\Stitcher\Lib\Parsedown;
use Symfony\Component\Finder\Finder;

/**
 * The MarkDownParser takes a path to a markdown file and will parse it to HTML.
 */
class MarkdownParser implements Parser
{
    private $srcDir;
    private $parsedown;

    public function __construct(string $srcDir, Parsedown $parsedown) {
        $this->srcDir = $srcDir;
        $this->parsedown = $parsedown;
    }

    public function parse($path) {
        if (!strpos($path, '.md')) {
            $path .= '.md';
        }

        $files = Finder::create()->files()->in($this->srcDir)->path($path)->getIterator();
        $files->rewind();
        $markdownFile = $files->current();

        if (!$markdownFile) {
            return '';
        }

        $html = $this->parsedown->parse($markdownFile->getContents());

        return $html;
    }
}

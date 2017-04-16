<?php

namespace Brendt\Stitcher\Parser;

use Brendt\Stitcher\Config;
use Parsedown;
use Symfony\Component\Finder\Finder;

/**
 * The MarkDownParser takes a path to a markdown file and will parse it to HTML.
 */
class MarkdownParser implements Parser
{

    /**
     * @var string
     */
    private $srcDir;

    /**
     * MarkdownParser constructor.
     *
     * @param string $srcDir
     */
    public function __construct(string $srcDir) {
        $this->srcDir = $srcDir;
    }

    /**
     * @param string $path
     *
     * @return string
     */
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

        $html = Parsedown::instance()->parse($markdownFile->getContents());

        return $html;
    }

}

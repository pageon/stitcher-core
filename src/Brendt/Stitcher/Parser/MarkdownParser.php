<?php

namespace Brendt\Stitcher\Parser;

use Brendt\Stitcher\Lib\Browser;
use Brendt\Stitcher\Lib\Parsedown;

/**
 * The MarkDownParser takes a path to a markdown file and will parse it to HTML.
 */
class MarkdownParser implements Parser
{
    private $browser;
    private $parsedown;

    public function __construct(Browser $browser, Parsedown $parsedown)
    {
        $this->browser = $browser;
        $this->parsedown = $parsedown;
    }

    public function parse($path)
    {
        if (!strpos($path, '.md')) {
            $path .= '.md';
        }

        $files = $this->browser->src()->path($path)->files()->getIterator();
        $files->rewind();
        $markdownFile = $files->current();

        if (!$markdownFile) {
            return '';
        }

        $html = $this->parsedown->parse($markdownFile->getContents());

        return $html;
    }
}

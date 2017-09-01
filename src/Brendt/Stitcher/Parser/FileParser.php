<?php

namespace Brendt\Stitcher\Parser;

use Brendt\Stitcher\Lib\Browser;

/**
 * The FileParser takes a path and reads the file contents from that path.
 */
class FileParser implements Parser
{
    private $browser;

    public function __construct(Browser $browser)
    {
        $this->browser = $browser;
    }

    public function parse($path)
    {
        $files = $this->browser->src()->files()->path(trim($path, '/'))->getIterator();
        $files->rewind();
        $file = $files->current();

        if (!$file) {
            return '';
        }

        return $file->getContents();
    }
}

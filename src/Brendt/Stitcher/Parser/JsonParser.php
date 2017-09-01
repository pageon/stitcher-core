<?php

namespace Brendt\Stitcher\Parser;

use Brendt\Stitcher\Exception\ParserException;
use Brendt\Stitcher\Factory\ParserFactory;
use Brendt\Stitcher\Lib\Browser;
use Symfony\Component\Finder\SplFileInfo;

/**
 * The JsonParser take a path to one or more JSON files, and parses the content into an array.
 */
class JsonParser extends AbstractArrayParser
{
    private $browser;

    public function __construct(Browser $browser, ParserFactory $parserFactory)
    {
        parent::__construct($parserFactory);

        $this->browser = $browser;
    }

    public function parse($path = '*.json')
    {
        if (!strpos($path, '.json')) {
            $path .= '.json';
        }

        $data = [];
        /** @var SplFileInfo[] $files */
        $files = $this->browser->src()->path($path)->files();

        foreach ($files as $file) {
            $parsed = json_decode($file->getContents(), true);

            if (json_last_error() > 0 && $error = json_last_error_msg()) {
                throw new ParserException("{$file->getRelativePathname()}: {$error}");
            }

            if (!isset($parsed['entries'])) {
                $id = str_replace(".{$file->getExtension()}", '', $file->getFilename());
                $parsed = ['entries' => [$id => $parsed]];
            }

            $data += $parsed['entries'];
        }

        $data = $this->parseArrayData($data);

        return $data;
    }
}

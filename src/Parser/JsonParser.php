<?php

namespace Brendt\Stitcher\Parser;

use Brendt\Stitcher\Config;
use Brendt\Stitcher\Exception\ParserException;
use Symfony\Component\Finder\Finder;

/**
 * The JsonParser take a path to one or more JSON files, and parses the content into an array.
 */
class JsonParser extends AbstractArrayParser
{

    /**
     * @param string $path
     *
     * @return array
     * @throws ParserException
     */
    public function parse($path = '*.json') {
        if (!strpos($path, '.json')) {
            $path .= '.json';
        }

        $data = [];
        $root = Config::get('directories.src');
        $files = Finder::create()->files()->in("{$root}")->path($path);

        foreach ($files as $file) {
            $parsed = json_decode($file->getContents(), true);

            if (json_last_error() > 0 && $error = json_last_error_msg()) {
                throw new ParserException("{$file->getRelativePathname()}: {$error}");
            }

            if (isset($parsed['entries'])) {
                $data += $parsed['entries'];
            } else {
                $id = str_replace(".{$file->getExtension()}", '', $file->getFilename());
                $data[$id] = $parsed;
            }
        }

        $data = $this->parseArrayData($data);

        return $data;
    }

}

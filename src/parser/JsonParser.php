<?php

namespace brendt\stitcher\parser;

use brendt\stitcher\exception\ParserException;
use Symfony\Component\Finder\Finder;

class JsonParser extends AbstractArrayParser {

    public function parse($path = '*.json') {
        $finder = new Finder();
        $data = [];
        if (!strpos($path, '.json')) {
            $path .= '.json';
        }

        $files = $finder->files()->in("{$this->root}")->path($path);

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

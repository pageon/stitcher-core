<?php

namespace brendt\stitcher\parser;

use brendt\stitcher\exception\ParserException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class YamlParser extends AbstractArrayParser {

    public function parse($path = '*.yml') {
        $finder = new Finder();
        $data = [];
        if (!strpos($path, '.yml')) {
            $path .= '.yml';
        }

        $files = $finder->files()->in("{$this->root}")->path($path);

        foreach ($files as $file) {
            try {
                $parsed = Yaml::parse($file->getContents());

                if (isset($parsed['entries'])) {
                    $data += $parsed['entries'];
                } else {
                    $id = str_replace(".{$file->getExtension()}", '', $file->getFilename());
                    $data[$id] = $parsed;
                }
            } catch (ParseException $e) {
                throw new ParserException("{$file->getRelativePathname()}: {$e->getMessage()}");
            }
        }

        $data = $this->parseArrayData($data);

        return $data;
    }

}

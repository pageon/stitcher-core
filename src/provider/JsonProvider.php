<?php

namespace brendt\stitcher\provider;

use Symfony\Component\Finder\Finder;

class JsonProvider extends AbstractArrayProvider {

    public function parse($path = '*.json') {
        $finder = new Finder();
        $data = [];
        if (!strpos($path, '.json')) {
            $path .= '.json';
        }

        $files = $finder->files()->in("{$this->root}")->path($path);

        foreach ($files as $file) {
            $data += json_decode($file->getContents(), true);
        }

        $data = $this->parseArrayData($data);

        return $data;
    }

}

<?php

namespace brendt\stitcher\dataProvider;

use Symfony\Component\Finder\Finder;

class JsonProvider extends FileProvider {

    public function parse($path = '*.json') {
        $finder = new Finder();
        $data = [];
        if (!strpos($path, '.json')) {
            $path .= '.json';
        }

        $files = $finder->files()->in("{$this->root}/data")->name($path);

        foreach ($files as $file) {
            $data += json_decode($file->getContents(), true);
        }

        foreach ($data as $id => $entry) {
            if (isset($entry['id'])) {
                continue;
            }

            $data[$id]['id'] = $id;
        }

        return $data;
    }

}

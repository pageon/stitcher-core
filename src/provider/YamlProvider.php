<?php

namespace brendt\stitcher\provider;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class YamlProvider extends FileProvider {

    public function parse($path = '*.yml') {
        $finder = new Finder();
        $data = [];
        if (!strpos($path, '.yml')) {
            $path .= '.yml';
        }

        $files = $finder->files()->in("{$this->root}/data")->name($path);

        foreach ($files as $file) {
            $data += Yaml::parse($file->getContents());
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

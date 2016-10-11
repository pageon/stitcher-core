<?php

namespace brendt\stitcher\provider;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class YamlProvider extends AbstractArrayProvider {

    public function parse($path = '*.yml') {
        $finder = new Finder();
        $data = [];
        if (!strpos($path, '.yml')) {
            $path .= '.yml';
        }

        $files = $finder->files()->in("{$this->root}")->path($path);

        foreach ($files as $file) {
            $data += Yaml::parse($file->getContents());
        }

        $data = $this->parseArrayData($data);

        return $data;
    }

}

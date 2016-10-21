<?php

namespace brendt\stitcher\provider;

use brendt\stitcher\Config;
use Symfony\Component\Finder\Finder;

class FileProvider implements Provider {

    /**
     * @param $path
     *
     * @return string
     */
    public function parse($path) {
        $finder = new Finder();
        $files = $finder->files()->in(Config::get('directories.src'))->path(trim($path, '/'));
        $data = '';

        foreach ($files as $file) {
            $data .= $file->getContents();
        }

        return $data;
    }

}

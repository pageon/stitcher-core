<?php

namespace brendt\stitcher\provider;

use brendt\stitcher\exception\ProviderException;
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
            $parsed = json_decode($file->getContents(), true);

            if (json_last_error() > 0 && $error = json_last_error_msg()) {
                throw new ProviderException("{$file->getRelativePathname()}: {$error}");
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

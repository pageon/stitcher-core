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
            try {
                $data += json_decode($file->getContents(), true);
            } catch (\Error $e) {
                if ($error = json_last_error_msg()) {
                    throw new ProviderException("{$file->getRelativePathname()}: {$error}");
                }
            }

        }

        $data = $this->parseArrayData($data);

        return $data;
    }

}

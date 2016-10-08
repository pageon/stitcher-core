<?php

namespace brendt\stitcher\provider;

use brendt\stitcher\factory\ProviderFactory;
use Symfony\Component\Finder\Finder;

class FolderProvider extends AbstractProvider  {
    
    public function parse($path, $parseSingle = false) {
        $data = [];
        $path = trim($path, '/');
        $finder = new Finder();
        $files = $finder->files()->in("{$this->root}/{$path}")->name('*.*');
        $factory = new ProviderFactory("{$this->root}/{$path}");

        foreach ($files as $file) {
            $provider = $factory->getProvider($file->getFilename());

            $id = str_replace(".{$file->getExtension()}", '', $file->getFilename());

            $data[$id] = $provider->parse($file->getRelativePathname(), true);
        }

        return $data;
    }
}

<?php

namespace brendt\stitcher\provider;

use brendt\stitcher\factory\ProviderFactory;
use Symfony\Component\Finder\Finder;

class FolderProvider extends AbstractProvider {

    /**
     * @param $path
     *
     * @return array
     */
    public function parse($path) {
        $data = [];
        $path = trim($path, '/');
        $finder = new Finder();
        $files = $finder->files()->in("{$this->root}/data/{$path}")->name('*.*');
        $factory = new ProviderFactory();

        foreach ($files as $file) {
            $provider = $factory->getProvider($file->getFilename());

            $id = str_replace(".{$file->getExtension()}", '', $file->getFilename());

            $data[$id] = $provider->parse($file->getRelativePathname());
        }

        return $data;
    }
}

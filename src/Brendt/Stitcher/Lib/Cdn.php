<?php

namespace Brendt\Stitcher\Lib;

use Symfony\Component\Filesystem\Filesystem;

class Cdn
{
    private $browser;
    private $files;
    private $enableCache;

    public function __construct(Browser $browser, array $files, bool $enableCache)
    {
        $this->browser = $browser;
        $this->files = $files;
        $this->enableCache = $enableCache;
    }

    public function save()
    {
        foreach ($this->files as $resource) {
            $resource = trim($resource, '/');
            $publicResourcePath = "{$this->browser->getPublicDir()}/{$resource}";

            if ($this->enableCache && file_exists($publicResourcePath)) {
                continue;
            }

            $sourceResourcePath = "{$this->browser->getSrcDir()}/{$resource}";
            $this->copyCdnFiles($sourceResourcePath, $publicResourcePath);
        }
    }

    private function copyCdnFiles(string $sourcePath, string $publicPath)
    {
        $fs = new Filesystem();

        if (is_dir($sourcePath)) {
            $fs->mirror($sourcePath, $publicPath);
        } else {
            $fs->copy($sourcePath, $publicPath, true);
        }
    }
}

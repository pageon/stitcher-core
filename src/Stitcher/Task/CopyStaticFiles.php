<?php

namespace Stitcher\Task;

use Stitcher\File;
use Stitcher\Task;
use Symfony\Component\Filesystem\Filesystem;

class CopyStaticFiles implements Task
{
    /** @var \Symfony\Component\Filesystem\Filesystem */
    private $fs;

    /** @var array */
    private $staticFiles;

    /** @var bool */
    private $cacheStaticFiles;

    /** @var string */
    private $publicDirectory;

    public function __construct(
        string $publicDirectory,
        array $staticFiles,
        bool $cacheStaticFiles
    ) {
        $this->fs = new Filesystem();
        $this->publicDirectory = $publicDirectory;
        $this->staticFiles = $staticFiles;
        $this->cacheStaticFiles = $cacheStaticFiles;
    }

    public function execute(): void
    {
        foreach ($this->staticFiles as $staticFile) {
            $staticFile = trim($staticFile, '/');

            $publicPath = File::path("{$this->publicDirectory}/{$staticFile}");

            if ($this->cacheStaticFiles && $this->fs->exists($publicPath)) {
                continue;
            }

            $sourcePath = File::path($staticFile);

            $this->copyStaticFile($sourcePath, $publicPath);
        }
    }

    private function copyStaticFile(
        string $sourcePath,
        string $publicPath
    ): void {
        if (is_dir($sourcePath)) {
            $this->fs->mirror($sourcePath, $publicPath);

            return;
        }

        $this->fs->copy($sourcePath, $publicPath);
    }
}

<?php

namespace Stitcher\Application;

class ProductionServer extends Server
{
    protected $rootDirectory;

    public function __construct(string $rootDirectory)
    {
        $this->rootDirectory = $rootDirectory;
    }

    public static function make(string $rootDirectory): ProductionServer
    {
        return new self($rootDirectory);
    }

    protected function handleStaticRoute(): ?string
    {
        $path = $this->getCurrentPath();

        $filename = ltrim($path === '/' ? 'index.html' : "{$path}.html", '/');

        return @file_get_contents("{$this->rootDirectory}/{$filename}");
    }
}

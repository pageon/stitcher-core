<?php

namespace Stitcher\Application;

class ProductionServer extends Server
{
    protected $rootDirectory;
    protected $uri;

    public function __construct(string $rootDirectory, string $uri = null)
    {
        $this->rootDirectory = $rootDirectory;
        $this->uri = $uri;
    }

    public static function make(string $rootDirectory, string $uri = null): ProductionServer
    {
        return new self($rootDirectory, $uri);
    }

    protected function handleStaticRoute(): ?string
    {
        $path = $this->getCurrentPath();

        $filename = ltrim($path === '/' ? 'index.html' : "{$path}.html", '/');

        return @file_get_contents("{$this->rootDirectory}/{$filename}");
    }
}

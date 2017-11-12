<?php

namespace Stitcher\Application;

class ProductionServer
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

    public function run(): string
    {
        $uri = $this->uri ?? $_SERVER['REQUEST_URI'];
        $filename = ltrim($uri === '/' ? 'index.html' : "{$uri}.html", '/');

        return @file_get_contents("{$this->rootDirectory}/{$filename}");
    }
}

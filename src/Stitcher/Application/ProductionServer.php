<?php

namespace Stitcher\Application;

class ProductionServer
{
    protected $html;

    public function __construct(string $rootDirectory, string $uri = null)
    {
        $uri = $uri ?? $_SERVER['SCRIPT_NAME'];
        $filename = ltrim($uri === '/' ? 'index.html' : "{$uri}.html", '/');

        $this->html = @file_get_contents("{$rootDirectory}/{$filename}");
    }

    public static function make(string $rootDirectory, string $uri = null): ProductionServer
    {
        return new self($rootDirectory, $uri);
    }

    public function run(): string
    {
        return $this->html;
    }
}

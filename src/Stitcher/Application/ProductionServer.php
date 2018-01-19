<?php

namespace Stitcher\Application;

use GuzzleHttp\Psr7\Response;

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

    protected function handleStaticRoute(): ?Response
    {
        $path = $this->getCurrentPath();

        $filename = ltrim($path === '/' ? 'index.html' : "{$path}.html", '/');

        $body = @file_get_contents("{$this->rootDirectory}/{$filename}");

        if (!$body) {
            return null;
        }

        return new Response(200, [], $body);
    }
}

<?php

namespace Stitcher\Application;

use Stitcher\Exception\Http;

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

    public function run(): string
    {
        if ($html = $this->handleStaticRoute()) {
            return $html;
        }

        if ($response = $this->handleDynamicRoute()) {
            return $response->getBody()->getContents();
        }

        throw Http::notFound($this->getRequest()->getUri());
    }

    protected function handleStaticRoute(): ?string
    {
        $path = $this->getCurrentPath();

        $filename = ltrim($path === '/' ? 'index.html' : "{$path}.html", '/');

        return @file_get_contents("{$this->rootDirectory}/{$filename}");
    }
}

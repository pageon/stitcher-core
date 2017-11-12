<?php

namespace Stitcher\Application;

use Stitcher\Command\PartialParse;
use Stitcher\Exception\Http;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class DevelopmentServer
{
    protected $rootDirectory;
    protected $uri = null;
    protected $partialParse;

    public function __construct(
        string $rootDirectory,
        PartialParse $partialParse,
        string $uri = null
    ) {
        $this->rootDirectory = $rootDirectory;
        $this->uri = $uri;
        $this->partialParse = $partialParse;
    }

    public static function make(
        string $rootDirectory,
        PartialParse $partialParse,
        string $uri = null
    ): DevelopmentServer
    {
        return new self($rootDirectory, $partialParse, $uri);
    }

    public function run(): string
    {
        $uri = $this->uri ?? $_SERVER['REQUEST_URI'];

        $this->partialParse->setFilter($uri);

        try {
            $this->partialParse->execute();

            $filename = ltrim($uri === '/' ? 'index.html' : "{$uri}.html", '/');

            return @file_get_contents("{$this->rootDirectory}/{$filename}");
        } catch (ResourceNotFoundException $e) {
            throw Http::notFound($uri);
        }
    }
}

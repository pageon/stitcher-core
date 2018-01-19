<?php

namespace Stitcher\Application;

use Stitcher\Task\PartialParse;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class DevelopmentServer extends Server
{
    protected $rootDirectory;
    protected $path = null;
    protected $partialParse;

    public function __construct(
        string $rootDirectory,
        PartialParse $partialParse,
        string $path = null
    ) {
        $this->rootDirectory = $rootDirectory;
        $this->path = $path;
        $this->partialParse = $partialParse;
    }

    public static function make(
        string $rootDirectory,
        PartialParse $partialParse,
        string $path = null
    ): DevelopmentServer
    {
        return new self($rootDirectory, $partialParse, $path);
    }

    protected function handleStaticRoute(): ?string
    {
        $path = $this->path ?? $this->getCurrentPath();

        $this->partialParse->setFilter($path);

        try {
            $this->partialParse->execute();

            $filename = ltrim($path === '/' ? 'index.html' : "{$path}.html", '/');

            return (string) @file_get_contents("{$this->rootDirectory}/{$filename}");
        } catch (ResourceNotFoundException $e) {
            return null;
        }
    }
}

<?php

namespace Stitcher\Application;

use Stitcher\Command\PartialParse;

class DevelopmentServer
{
    private $html;

    public function __construct(
        PartialParse $partialParse,
        string $rootDirectory,
        string $uri = null
    ) {
        $uri = $uri ?? $_SERVER['SCRIPT_NAME'];

        $partialParse->setFilter($uri);
        $partialParse->execute();

        $filename = ltrim($uri === '/' ? 'index.html' : "{$uri}.html", '/');

        $this->html = @file_get_contents("{$rootDirectory}/{$filename}");
    }

    public static function make(
        PartialParse $partialParse,
        string $rootDirectory,
        string $uri = null
    ): DevelopmentServer
    {
        return new self($partialParse, $rootDirectory, $uri);
    }

    public function run(): string
    {
        return $this->html;
    }
}

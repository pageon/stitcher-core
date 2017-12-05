<?php

namespace Stitcher\Renderer\Extension;

use Stitcher\File;
use Stitcher\Renderer\Extension;
use Symfony\Component\Filesystem\Filesystem;

class Js implements Extension
{
    protected $publicDirectory;

    protected $defer = false;
    protected $async = false;

    public function __construct(
        string $publicDirectory
    ) {
        $this->publicDirectory = $publicDirectory;
    }

    public function name(): string
    {
        return 'js';
    }

    public function link(string $src): string
    {
        [$url, $content] = $this->parseSource($src);

        $script = "<script src=\"{$url}\"";

        if ($this->defer) {
            $script .= ' defer';
        }

        if ($this->async) {
            $script .= ' async';
        }

        $script .= "></script>";

        return $script;
    }

    public function inline(string $src): string
    {
        [$url, $content] = $this->parseSource($src);

        return '<script>' . $content . '</script>';
    }

    public function defer(): Js
    {
        $this->defer = true;

        return $this;
    }

    public function async(): Js
    {
        $this->async = true;

        return $this;
    }

    public function parseSource(string $src): array
    {
        $src = ltrim($src, '/');

        ['dirname' => $dirname, 'filename' => $filename, 'extension' => $extension] = pathinfo($src);

        $content = File::read($src);

        $path = "{$dirname}/{$filename}.{$extension}";

        $this->saveFile($path, $content);

        return ["/{$path}", $content];
    }

    protected function saveFile(string $path, string $content): void
    {
        $fs = new Filesystem();

        $fs->dumpFile(
            File::path("{$this->publicDirectory}/{$path}"),
            $content
        );
    }
}

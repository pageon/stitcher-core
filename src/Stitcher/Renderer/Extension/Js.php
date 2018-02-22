<?php

namespace Stitcher\Renderer\Extension;

use Pageon\Html\Source;
use Stitcher\Exception\StitcherException;
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
        $source = $this->parseSource($src);

        $script = "<script src=\"{$source->url()}\"";

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
        $source = $this->parseSource($src);

        return '<script>' . $source->content() . '</script>';
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

    public function parseSource(string $src): Source
    {
        $src = ltrim($src, '/');

        ['dirname' => $dirname, 'filename' => $filename, 'extension' => $extension] = pathinfo($src);

        $content = File::read($src);

        if (!$content) {
            throw StitcherException::fileNotFound(File::path($src));
        }

        $path = "{$dirname}/{$filename}.{$extension}";

        $this->saveFile($path, $content);

        return new Source("/{$path}", $content);
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

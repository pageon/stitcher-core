<?php

namespace Stitcher\Renderer\Extension;

use Leafo\ScssPhp\Compiler as Sass;
use Pageon\Html\Source;
use Stitcher\File;
use Stitcher\Renderer\Extension;
use Symfony\Component\Filesystem\Filesystem;

class Css implements Extension
{
    /** @var string */
    protected $publicDirectory;

    /** @var \Leafo\ScssPhp\Compiler */
    protected $sass;

    /** @var bool */
    private $minify = false;

    public function __construct(
        string $publicDirectory,
        Sass $sass
    ) {
        $this->publicDirectory = $publicDirectory;
        $this->sass = $sass;
    }

    public function setMinify(bool $minify): self
    {
        $this->minify = $minify;

        return $this;
    }

    public function name(): string
    {
        return 'css';
    }

    public function link(string $src): string
    {
        $source = $this->parseSource($src);

        return "<link rel=\"stylesheet\" href=\"{$source->url()}\" />";
    }

    public function inline(string $src): string
    {
        $source = $this->parseSource($src);

        return '<style>' . $source->content() . '</style>';
    }

    public function parseSource(string $src): Source
    {
        $src = ltrim($src, '/');

        ['dirname' => $dirname, 'filename' => $filename, 'extension' => $extension] = pathinfo($src);

        $content = File::read($src);

        if (\in_array($extension, ['scss', 'sass'])) {
            $content = $this->sass->compile($content);
            $extension = 'css';
        }

        if ($this->minify) {
//            $content = $this->minifier->run($content);
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

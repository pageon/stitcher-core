<?php

namespace Stitcher\Renderer\Extension;

use Leafo\ScssPhp\Compiler as Sass;
use Stitcher\File;
use Stitcher\Renderer\Extension;
use Symfony\Component\Filesystem\Filesystem;

class Css implements Extension
{
    private $sourceDirectory;
    private $publicDirectory;
    private $sass;

    public function __construct(
        string $sourceDirectory,
        string $publicDirectory,
        Sass $sass
    ) {
        $this->sourceDirectory = $sourceDirectory;
        $this->publicDirectory = $publicDirectory;
        $this->sass = $sass;
    }

    public function name(): string
    {
        return 'css';
    }

    public function inline(string $src): string
    {
        [$url, $content] = $this->handle($src);

        return '<style>' . $content . '</style>';
    }

    public function href(string $src): string
    {
        [$url, $content] = $this->handle($src);

        return "<link rel=\"stylesheet\" href=\"{$url}\" />";
    }

    public function handle(string $src): array
    {
        $src = ltrim($src, '/');

        ['dirname' => $dirname, 'filename' => $filename, 'extension' => $extension] = pathinfo($src);

        $content = File::read("{$this->sourceDirectory}/{$src}");

        if (in_array($extension, ['scss', 'sass'])) {
            $content = $this->sass->compile($content);
            $extension = 'css';
        }

        $path = "{$dirname}/{$filename}.{$extension}";

        $this->saveCss($path, $content);

        return ["/{$path}", $content];
    }

    protected function saveCss(string $path, string $content): void
    {
        $fs = new Filesystem();

        $fs->dumpFile(
            File::path("{$this->publicDirectory}/{$path}"),
            $content
        );
    }
}

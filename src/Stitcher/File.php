<?php

namespace Stitcher;

use Symfony\Component\Filesystem\Filesystem;

class File
{
    private static $fs;
    private static $base;

    public static function base(?string $base): void
    {
        self::$base = rtrim($base, '/');
    }

    public static function path(string $path = null): string
    {
        $path = str_replace(self::$base, '', $path);
        $path = ltrim($path, '/');
        $path = "/{$path}";

        return self::$base . $path;
    }

    public static function read(string $path): ?string
    {
        $path = self::path($path);

        if (! file_exists(self::path($path))) {
            return null;
        }

        $contents = @file_get_contents(self::path($path));

        return $contents ?? null;
    }

    public static function write(string $path, $content = null): void
    {
        if (! self::$fs) {
            self::$fs = new Filesystem();
        }

        self::$fs->dumpFile(self::path($path), $content);
    }
}

<?php

namespace Stitcher\Exception;

class FileNotFound extends StitcherException
{
    public static function withPath(string $path, string $source = null): self
    {
        return new self("File not found in path `{$path}`", $source);
    }

    public static function staticFile(string $path): self
    {
        return self::withPath($path, 'This file was tried to be copied as a static file. Please see your `staticFiles` configuration.' );
    }
}

<?php

namespace Pageon\Html\Image;

class Image
{
    private $src;
    private $srcset = [];
    private $sizes;
    private $alt;

    public function __construct(string $src, ?string $sizes = null, ?string $alt = null)
    {
        $this->src = "/{$src}";
        $this->sizes = $sizes;
        $this->alt = $alt;
    }

    public static function make(string $src, ?string $sizes = null, ?string $alt = null): Image
    {
        return new self($src, $sizes, $alt);
    }

    public function src(): string
    {
        return $this->src;
    }

    public function srcset(): string
    {
        return implode(', ', $this->srcset);
    }

    public function sizes(): ?string
    {
        return $this->sizes;
    }

    public function alt(): ?string
    {
        return $this->alt;
    }

    public function addSrcset(string $src, int $width): Image
    {
        $src = ltrim($src, '/');

        $this->srcset[] = "/{$src} {$width}w";

        return $this;
    }
}

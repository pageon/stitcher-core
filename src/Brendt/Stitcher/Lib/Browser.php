<?php

namespace Brendt\Stitcher\Lib;

use Symfony\Component\Finder\Finder;

class Browser
{
    private $srcDir;
    private $publicDir;
    private $templateDir;
    private $cacheDir;

    public function __construct(string $srcDir, string $publicDir, string $templateDir, string $cacheDir)
    {
        $this->srcDir = $srcDir;
        $this->publicDir = $publicDir;
        $this->templateDir = $templateDir;
        $this->cacheDir = $cacheDir;
    }

    public function src() : Finder
    {
        return Finder::create()->in($this->srcDir);
    }

    public function public () : Finder
    {
        return Finder::create()->in($this->publicDir);
    }

    public function template() : Finder
    {
        return Finder::create()->in($this->templateDir);
    }

    public function cache() : Finder
    {
        return Finder::create()->in($this->cacheDir);
    }

    public function getSrcDir() : string
    {
        return $this->srcDir;
    }

    public function getPublicDir() : string
    {
        return $this->publicDir;
    }

    public function getTemplateDir() : string
    {
        return $this->templateDir;
    }

    public function getCacheDir() : string
    {
        return $this->cacheDir;
    }
}

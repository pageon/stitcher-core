<?php

namespace brendt\stitcher\element;

use brendt\stitcher\Config;
use Symfony\Component\Filesystem\Filesystem;

class Image {

    protected $source = null;

    protected $sources = [];

    protected $sourcePath;

    protected $filesystem;

    protected $name;

    protected $extension;

    protected $publicDir;

    public function __construct($pathname, $relativePathname) {
        $this->filesystem = new Filesystem();
        $this->publicDir = Config::get('directories.public');

        $publicPath = "{$this->publicDir}/{$relativePathname}";
        if (!$this->filesystem->exists($publicPath)) {
            $this->filesystem->copy($pathname, $publicPath);
        }

        $this->sourcePath = $pathname;
        $pathParts = explode('.', $relativePathname);
        $this->extension = array_pop($pathParts);
        $this->name = implode('', $pathParts);
        $this->source = new ImageSource($pathname, $relativePathname);

        $this->addSource($this->source);
    }

    public function addSource(ImageSource $source) {
        $this->sources[] = $source;
    }

    public function getWidth() {
        return $this->source->getDimensions()['width'];
    }

    public function getHeight() {
        return $this->source->getDimensions()['height'];
    }

    public function scale($width, $height) {
        $imageFile = $this->getImageFile($this->sourcePath);

        if (!$imageFile) {
            return;
        }

        $name = "{$this->name}-{$width}x{$height}.{$this->extension}";

        $imageDestinationFile = imagescale($imageFile, $width);

        $this->saveImageFile($name, $imageDestinationFile);
    }

    protected function getImageFile($path) {
        $imageFile = null;

        if ($this->extension ==='jpg') {
            $imageFile = imagecreatefromjpeg($path);
        } elseif ($this->extension === 'png') {
            $imageFile = imagecreatefrompng($path);
        }

        return $imageFile;
    }

    protected function saveImageFile($name, $imageFile) {
        $fs = $this->filesystem;
        $nameTrimmed = trim($name, '/');

        $filePath = "{$this->publicDir}/{$nameTrimmed}";

        if ($fs->exists($filePath)) {
            $fs->remove($filePath);
            $fs->touch($filePath);
        }

        if ($this->extension === 'jpg') {
            imagejpeg($imageFile, $filePath, 100);
        } elseif ($this->extension === 'png') {
            imagepng($imageFile, $filePath);
        }

        $this->addSource(new ImageSource($filePath, $name));
    }

    public function renderSrc() {
        return $this->source->getSource();
    }

    public function renderSrcset() {
        return implode(', ', $this->sources);
    }

}

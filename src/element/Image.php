<?php

namespace brendt\stitcher\element;

use brendt\stitcher\Config;
use Intervention\Image\ImageManager;
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
        /** @var ImageManager $imageEngine */
        $imageEngine = Config::getDependency('engine.image');
        $name = "{$this->name}-{$width}x{$height}.{$this->extension}";

        $imageEngine->make($this->sourcePath)
            ->resize($width, $height)
            ->save(Config::get('directories.public') . "/$name");
    }

    public function renderSrc() {
        return $this->source->getSource();
    }

    public function renderSrcset() {
        return implode(', ', $this->sources);
    }

}

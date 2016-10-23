<?php

namespace brendt\stitcher\element;

use Symfony\Component\Finder\SplFileInfo;

class ImageSource {

    protected $source;

    private $width;

    private $height;

    public function __construct($path, $name) {
        $this->source = '/' . trim($name, '/');

        $dimensions = getimagesize($path);

        $this->width = $dimensions[0];
        $this->height = $dimensions[1];
    }

    public function getSource() {
        return $this->source;
    }

    public function getDimensions() {
        return [
            'width' => $this->width,
            'height' => $this->height,
        ];
    }

    public function __toString() {
        return "{$this->source} {$this->width}w";

    }

}

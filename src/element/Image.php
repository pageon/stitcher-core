<?php

namespace brendt\stitcher\element;

class Image {

    public $srcset = null;

    public $src = null;

    protected $source = null;

    protected $sources = [];

    public function __construct($path, $name) {
        $this->source = new ImageSource($path, $name);

        $this->addSource($this->source);
        $this->src = $this->source->getSource();
    }

    public function addSource(ImageSource $source) {
        $this->sources[] = $source;
        $this->srcset = implode(', ', $this->sources);
    }

    public function getWidth() {
        return $this->source->getDimensions()['width'];
    }

    public function getHeight() {
        return $this->source->getDimensions()['height'];
    }

}

<?php

namespace Stitcher\Variable;

use Pageon\Html\Image\ImageFactory;

class ImageVariable extends AbstractVariable
{
    private $imageFactory;
    private $alt;

    public function __construct(string $src, ImageFactory $imageFactory, ?string $alt = '')
    {
        parent::__construct($src);

        $this->imageFactory = $imageFactory;
        $this->alt = $alt;
    }

    public static function make(string $value, ImageFactory $imageFactory, ?string $alt = '') : ImageVariable
    {
        return new self($value, $imageFactory, $alt);
    }

    public function parse() : AbstractVariable
    {
        $image = $this->imageFactory->create($this->unparsed);

        $this->parsed = [
            'src'    => $image->src(),
            'srcset' => $image->srcset(),
            'alt'    => $this->alt,
        ];

        return $this;
    }
}

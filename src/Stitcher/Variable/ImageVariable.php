<?php

namespace Stitcher\Variable;

use Pageon\Html\Image\ImageFactory;

class ImageVariable extends AbstractVariable
{
    private $imageFactory;

    public function __construct($attributes, ImageFactory $imageFactory)
    {
        parent::__construct($attributes);

        $this->imageFactory = $imageFactory;
    }

    public static function make(
        $attributes,
        ImageFactory $imageFactory
    ) : ImageVariable {
        return new self($attributes, $imageFactory);
    }

    public function parse() : AbstractVariable
    {
        $src = $this->unparsed['src'] ?? $this->unparsed;
        $alt = $this->unparsed['alt'] ?? null;

        $image = $this->imageFactory->create($src);

        $this->parsed = [
            'src'    => $image->src(),
            'srcset' => $image->srcset(),
            'alt'    => $alt,
        ];

        return $this;
    }
}

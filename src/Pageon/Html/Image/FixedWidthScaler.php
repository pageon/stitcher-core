<?php

namespace Pageon\Html\Image;

use Intervention\Image\Image as ScaleableImage;

class FixedWidthScaler implements Scaler
{
    private $fixedWidths;

    public function __construct(array $fixedWidths)
    {
        $this->fixedWidths = $fixedWidths;
    }

    public static function make(array $fixedWidths): FixedWidthScaler
    {
        return new self($fixedWidths);
    }

    public function getVariations(ScaleableImage $scaleableImage): array
    {
        $width = $scaleableImage->getWidth();
        $height = $scaleableImage->getHeight();
        $ratio = $width / $height;
        $variations = [];

        foreach ($this->fixedWidths as $fixedWidth) {
            $variations[$fixedWidth] = round($fixedWidth * $ratio);
        }

        return $variations;
    }
}

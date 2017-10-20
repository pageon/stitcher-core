<?php

namespace Pageon\Html\Image;

use Intervention\Image\Image as ScaleableImage;

interface Scaler
{
    public function getVariations(ScaleableImage $scaleableImage) : array;
}

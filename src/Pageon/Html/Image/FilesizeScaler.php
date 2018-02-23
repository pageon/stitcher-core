<?php

namespace Pageon\Html\Image;

use Intervention\Image\Image as ScaleableImage;

class FilesizeScaler implements Scaler
{
    private $stepModifier = 0.2;

    public function __construct(float $stopModifier = 0.2)
    {
        $this->stepModifier = $stopModifier;
    }

    public function getVariations(ScaleableImage $scaleableImage): array
    {
        $fileSize = $scaleableImage->filesize();
        $width = $scaleableImage->width();

        $ratio = $scaleableImage->height() / $width;
        $area = $width * $width * $ratio;
        $pixelPrice = $fileSize / $area;

        $stepAmount = $fileSize * $this->stepModifier;

        $variations = [];

        do {
            $newWidth = (int) floor(sqrt(($fileSize / $pixelPrice) / $ratio));

            $variations[$newWidth] = (int) $newWidth * $ratio;

            $fileSize -= $stepAmount;
        } while ($fileSize > 0);

        return $variations;
    }
}

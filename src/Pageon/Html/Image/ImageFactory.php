<?php

namespace Pageon\Html\Image;

use Intervention\Image\ImageManager;
use Intervention\Image\Image as ScaleableImage;
use Stitcher\File;
use Symfony\Component\Filesystem\Filesystem;

class ImageFactory
{
    /** @var \Pageon\Html\Image\Scaler */
    private $scaler;

    /** @var string */
    private $sourceDirectory;

    /** @var string */
    private $publicDirectory;

    /** @var \Intervention\Image\ImageManager */
    private $imageManager;

    /** @var bool */
    private $cache = false;

    public function __construct(
        string $sourceDirectory,
        string $publicDirectory,
        Scaler $scaler
    ) {
        $this->sourceDirectory = rtrim($sourceDirectory, '/');
        $this->publicDirectory = rtrim($publicDirectory, '/');
        $this->scaler = $scaler;
        $this->imageManager = new ImageManager([
            'driver' => 'gd',
        ]);
    }

    public static function make(
        string $sourceDirectory,
        string $publicDirectory,
        Scaler $scaler
    ): ImageFactory
    {
        return new self($sourceDirectory, $publicDirectory, $scaler);
    }

    public function enableCaching(bool $cache): ImageFactory
    {
        $this->cache = $cache;

        return $this;
    }

    public function create($src): Image
    {
        $srcPath = ltrim($src, '/');
        $image = Image::make($srcPath);

        $this->copySourceImageToDestination($srcPath);
        $scaleableImage = $this->imageManager->make("{$this->publicDirectory}/{$srcPath}");

        $variations = $this->scaler->getVariations($scaleableImage);
        $image->addSrcset($image->src(), $scaleableImage->getWidth());

        foreach ($variations as $width => $height) {
            if (!$width) {
                continue;
            }

            $this->createScaledImage($image, $width, $height, $scaleableImage);
        }

        return $image;
    }

    private function createScaledImage(
        Image $image,
        int $width,
        int $height,
        ScaleableImage $scaleableImage
    ): void {
        $scaleableImageClone = clone $scaleableImage;

        $scaledFileName = $this->createScaledFileName($image, $width, $height);

        $image->addSrcset($scaledFileName, $width);

        if ($this->cache && file_exists("{$this->publicDirectory}/{$scaledFileName}")) {
            return;
        }

        $scaleableImageClone
            ->resize($width, $height)
            ->save("{$this->publicDirectory}/{$scaledFileName}");
    }

    private function createScaledFileName(Image $image, int $width, int $height): string
    {
        $srcPath = ltrim($image->src(), '/');
        $extension = pathinfo($srcPath, PATHINFO_EXTENSION);

        return str_replace(".{$extension}", "-{$width}x{$height}.{$extension}", $srcPath);
    }

    private function copySourceImageToDestination(string $srcPath): void
    {
        $fs = new Filesystem();

        $fs->copy(File::path($srcPath), "{$this->publicDirectory}/{$srcPath}");
    }
}

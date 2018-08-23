<?php

namespace Pageon\Html\Image;

use Intervention\Image\ImageManager;
use Intervention\Image\Image as ScaleableImage;
use Spatie\ImageOptimizer\OptimizerChain;
use Stitcher\File;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

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

    /** @var \Spatie\ImageOptimizer\OptimizerChain */
    protected $optimizer;

    /** @var bool */
    private $cache = false;

    public function __construct(
        string $sourceDirectory,
        string $publicDirectory,
        Scaler $scaler,
        OptimizerChain $optimizer = null
    ) {
        $this->sourceDirectory = rtrim($sourceDirectory, '/');
        $this->publicDirectory = rtrim($publicDirectory, '/');
        $this->scaler = $scaler;

        $this->imageManager = new ImageManager([
            'driver' => 'gd',
        ]);

        $this->optimizer = $optimizer;
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

        if ($this->cache && file_exists("{$this->publicDirectory}/{$srcPath}")) {
            return $this->createCachedImage($srcPath);
        }

        $image = Image::make($srcPath);

        $this->copySourceImageToDestination($srcPath);

        $scaleableImage = $this->imageManager->make("{$this->publicDirectory}/{$srcPath}");

        $this->optimize($scaleableImage->basePath());

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

        $this->optimize("{$this->publicDirectory}/{$scaledFileName}");
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

    private function createCachedImage(string $path): Image
    {
        $image = Image::make($path);

        $extension = pathinfo($path, PATHINFO_EXTENSION);

        $imageFilePath = pathinfo($image->src(), PATHINFO_DIRNAME);

        $imageFileName = pathinfo($image->src(), PATHINFO_FILENAME);

        $srcsetFiles = Finder::create()->files()
            ->in($this->publicDirectory . $imageFilePath)
            ->name("{$imageFileName}-*.{$extension}");

        foreach ($srcsetFiles->getIterator() as $srcsetFile) {
            $cachedFilename = $srcsetFile->getFilename();

            $size = (int) str_replace(".{$extension}", '', str_replace("{$imageFileName}-", '', $cachedFilename));

            $image->addSrcset("{$imageFilePath}/{$cachedFilename}", $size);
        }

        return $image;
    }

    private function optimize(string $path): void
    {
        if (! $this->optimizer) {
            return;
        }

        $this->optimizer->optimize($path);
    }
}

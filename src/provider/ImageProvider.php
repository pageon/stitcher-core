<?php

namespace brendt\stitcher\provider;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use brendt\stitcher\element\ImageSource;
use brendt\stitcher\element\Image;
use Symfony\Component\Finder\SplFileInfo;

class ImageProvider extends AbstractProvider {

    private static $dimensions = [
        '960x640' => [
            'width'  => 960,
            'height' => 640,
        ],
        '1024x768' => [
            'width'  => 1024,
            'height' => 768,
        ],
        '1920x1080' => [
            'width'  => 1920,
            'height' => 1080,
        ],
        '1600x1200' => [
            'width'  => 1600,
            'height' => 1200,
        ],
    ];

    /**
     * AbstractProvider constructor.
     *
     * @param        $root
     */
    public function __construct($root) {
        parent::__construct($root);
    }

    /**
     * @param $path
     *
     * @return array|mixed
     */
    public function parse($path) {
        $fs = new Filesystem();
        $finder = new Finder();
        $data = [];

        /** @var SplFileInfo[] $files */
        $files = $finder->files()->in($this->root)->path($path);

        foreach ($files as $file) {
            $imageName = str_replace(".{$file->getExtension()}", '', $file->getRelativePathname());
            $srcPath = "{$this->root}/{$file->getRelativePathname()}";
            $srcName = "/{$file->getRelativePathname()}";
            $fs->copy($file->getPathname(), $srcPath);

            $image = new Image($srcPath, $srcName);

            foreach (self::$dimensions as $dimensionName => $dimension) {
                $width = $dimension['width'];
                if ($width > $image->getWidth()) {
                    continue;
                }

                $targetPath = "{$this->root}/{$imageName}-{$dimensionName}.{$file->getExtension()}";
                $targetName = "{$imageName}-{$dimensionName}.{$file->getExtension()}";
                $targetSource = null;

                if (strpos($targetPath, '.jpg') !== false) {
                    $targetSource = imagecreatefromjpeg($srcPath);
                } elseif (strpos($targetPath, '.png') !== false) {
                    $targetSource = imagecreatefrompng($srcPath);
                }

                $target = imagescale($targetSource, $width);

                if ($target) {
                    if ($fs->exists($targetPath)) {
                        $fs->remove($targetPath);
                        $fs->touch($targetPath);
                    }

                    if (strpos($targetPath, '.jpg') !== false) {
                        imagejpeg($target, $targetPath, 100);
                    } elseif (strpos($targetPath, '.png') !== false) {
                        imagepng($target, $targetPath);
                    }

                    $image->addSource(new ImageSource($targetPath, "/{$targetName}"));
                }
            }

            $data[] = [
                'src' => $image->src,
                'srcset' => $image->srcset,
            ];
        }

        return count($data) > 1 ? $data : reset($data);
    }

}

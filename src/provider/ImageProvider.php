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
     * @var string
     */
    private $publicDir;

    /**
     * AbstractProvider constructor.
     *
     * @param        $root
     * @param string $publicDir
     */
    public function __construct($root, $publicDir = './public') {
        parent::__construct($root);

        $this->publicDir = $publicDir;
    }

    /**
     * @param $entry
     *
     * @return array|mixed
     */
    public function parse($entry) {
        $data = [];
        $defaults = [];
        $finder = new Finder();
        $path = null;

        if (is_array($entry)) {
            if (array_key_exists('src', $entry)) {
                $path = $entry['src'];
                unset($entry['src']);
            }

            foreach ($entry as $field => $value) {
                $defaults[$field] = $value;
            }
        } else {
            $path = $entry;
        }

        if (!$path) {
            return $data;
        }

        /** @var SplFileInfo[] $files */
        $files = $finder->files()->in($this->root)->path($path);

        foreach ($files as $file) {
            $image = new Image($file->getPathname(), $file->getRelativePathname(), $this->publicDir);

            foreach (self::$dimensions as $dimensionName => $dimension) {
                $width = $dimension['width'];
                if ($width > $image->getWidth()) {
                    continue;
                }
                $height = $dimension['height'];

                $image->scale($width, $height);
            }

            $data[] = [
                'src' => $image->renderSrc(),
                'srcset' => $image->renderSrcset(),
            ] + $defaults;
        }

        return count($data) > 1 ? $data : reset($data);
    }

}

<?php

namespace brendt\stitcher\provider;

use brendt\stitcher\Config;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use brendt\stitcher\element\ImageSource;
use brendt\stitcher\element\Image;
use Symfony\Component\Finder\SplFileInfo;

class ImageProvider extends AbstractProvider {

    /**
     * @var string
     */
    private $publicDir;

    /**
     * AbstractProvider constructor.
     */
    public function __construct() {
        parent::__construct();

        $this->publicDir = Config::get('directories.public');
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
            $image = new Image($file->getPathname(), $file->getRelativePathname());

            foreach (Config::get('image.dimensions') as $dimensionName => $dimension) {
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

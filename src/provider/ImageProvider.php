<?php

namespace brendt\stitcher\provider;

use brendt\image\ResponsiveFactory;
use brendt\stitcher\Config;

class ImageProvider extends AbstractProvider {

    /**
     * @var mixed
     */
    protected $imageDir;

    /**
     * @var ResponsiveFactory
     */
    protected $factory;

    /**
     * AbstractProvider constructor.
     */
    public function __construct() {
        parent::__construct();

        $this->imageDir = Config::get('directories.src');
        $this->factory = Config::getDependency('factory.image');
    }

    /**
     * @param $entry
     *
     * @return array|mixed
     */
    public function parse($entry) {
        $data = [];
        $defaults = [];
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

        $image = $this->factory->create($path);

        $data = [
            'src' => $image->src(),
            'srcset' => $image->srcset(),
            'sizes' => $image->sizes(),
        ] + $defaults;

        return $data;
    }

}

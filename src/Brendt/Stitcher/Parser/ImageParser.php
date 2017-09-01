<?php

namespace Brendt\Stitcher\Parser;

use Brendt\Image\ResponsiveFactory;
use Brendt\Stitcher\Stitcher;

/**
 * The ImageParser uses the ResponsiveFactory to create images from an entry (array of parsed data),
 * or a path to an image.
 *
 * @see \Brendt\Image\ResponsiveFactory::create()
 */
class ImageParser implements Parser
{
    private $stitcher;
    private $responsiveFactory;

    public function __construct(Stitcher $stitcher, ResponsiveFactory $responsiveFactory)
    {
        $this->stitcher = $stitcher;
        $this->responsiveFactory = $responsiveFactory;
    }

    public function parse($entry)
    {
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

        $image = $this->responsiveFactory->create($path);

        $data = [
            'src'    => $image->src(),
            'srcset' => $image->srcset(),
            'sizes'  => $image->sizes(),
        ];

        return $data + $defaults;
    }
}

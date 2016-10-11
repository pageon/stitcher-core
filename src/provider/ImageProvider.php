<?php

namespace brendt\stitcher\provider;

use Symfony\Component\Finder\Finder;

class ImageProvider extends AbstractProvider {

    public function parse($path, $parseSingle = false) {
        return 'IMG';
    }

}

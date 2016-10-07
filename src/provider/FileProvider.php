<?php

namespace brendt\stitcher\provider;

use Symfony\Component\Finder\Finder;

abstract class FileProvider implements Provider {

    /**
     * @var
     */
    protected $root;

    /**
     * FileDataProvider constructor.
     *
     * @param $root
     */
    public function __construct($root) {
        $this->root = $root;
    }

}

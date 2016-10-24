<?php

namespace brendt\stitcher\provider;

use brendt\stitcher\Config;
use brendt\stitcher\factory\ProviderFactory;

abstract class AbstractProvider implements Provider {

    /**
     * @var string
     */
    protected $root;

    /**
     * AbstractProvider constructor.
     */
    public function __construct() {
        $this->root = Config::get('directories.src');
    }

}

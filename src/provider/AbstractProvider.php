<?php

namespace brendt\stitcher\provider;

use brendt\stitcher\factory\ProviderFactory;

abstract class AbstractProvider implements Provider {

    /**
     * @var string
     */
    protected $root;

    /**
     * AbstractProvider constructor.
     *
     * @param $root
     */
    public function __construct($root) {
        $this->root = $root;
    }

}

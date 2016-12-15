<?php

namespace brendt\stitcher\parser;

use brendt\stitcher\Config;

abstract class AbstractParser implements Parser {

    /**
     * @var string
     */
    protected $root;

    /**
     * AbstractParser constructor.
     */
    public function __construct() {
        $this->root = Config::get('directories.src');
    }

}

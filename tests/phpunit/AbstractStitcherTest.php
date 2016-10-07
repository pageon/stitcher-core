<?php

use brendt\stitcher\Stitcher;

abstract class AbstractStitcherTest extends PHPUnit_Framework_TestCase {

    /**
     * @var string
     */
    protected $root;

    /**
     * StitcherTest constructor.
     */
    public function __construct() {
        parent::__construct();

        $this->root = './tests/src';
    }

    /**
     * @return Stitcher
     */
    protected function createStitcher() {
        return new Stitcher($this->root);
    }

}

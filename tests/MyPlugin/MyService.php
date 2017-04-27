<?php

namespace MyPlugin;

use Brendt\Stitcher\Stitcher;

class MyService
{

    /**
     * @var Stitcher
     */
    private $stitcher;

    /**
     * @var mixed
     */
    private $myParameter;

    /**
     * MyService constructor.
     *
     * @param          $myParameter
     * @param Stitcher $stitcher
     */
    public function __construct($myParameter, Stitcher $stitcher) {
        $this->myParameter = $myParameter;
        $this->stitcher = $stitcher;
    }

    /**
     * @return Stitcher
     */
    public function getStitcher() : Stitcher {
        return $this->stitcher;
    }

    /**
     * @return mixed
     */
    public function getMyParameter() {
        return $this->myParameter;
    }

}

<?php

namespace Brendt\Stitcher\Application;

use Symfony\Component\Console\Application;

class Console extends Application
{
    public function __construct()
    {
        parent::__construct('Stitcher Console');
    }
}

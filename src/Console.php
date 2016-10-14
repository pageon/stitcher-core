<?php

namespace brendt\stitcher;

use brendt\stitcher\command\GenerateCommand;
use brendt\stitcher\command\RoutesCommand;
use brendt\stitcher\command\SetupCommand;
use brendt\stitcher\command\RouteCommand;
use Symfony\Component\Console\Application;

class Console extends Application {

    public function __construct() {
        parent::__construct('Stitcher Console');

        $this->add(new SetupCommand());
        $this->add(new GenerateCommand());
        $this->add(new RoutesCommand());
        $this->add(new RouteCommand());
    }

}

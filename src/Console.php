<?php

namespace brendt\stitcher;

use brendt\stitcher\command\GenerateCommand;
use brendt\stitcher\command\RoutesCommand;
use brendt\stitcher\command\InstallCommand;
use brendt\stitcher\command\RouteCommand;
use brendt\stitcher\command\CleanCommand;
use Symfony\Component\Console\Application;

class Console extends Application {

    public function __construct() {
        parent::__construct('Stitcher Console');

        $this->add(new InstallCommand());
        $this->add(new GenerateCommand());
        $this->add(new CleanCommand());
        $this->add(new RoutesCommand());
        $this->add(new RouteCommand());
    }

}

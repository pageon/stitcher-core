<?php

namespace Brendt\Stitcher;

use Brendt\Stitcher\Command\CleanCommand;
use Brendt\Stitcher\Command\GenerateCommand;
use Brendt\Stitcher\Command\InstallCommand;
use Brendt\Stitcher\Command\RouterDispatchCommand;
use Brendt\Stitcher\Command\RouterListCommand;
use Symfony\Component\Console\Application;

class Console extends Application {

    public function __construct() {
        parent::__construct('Stitcher Console');

        $this->add(new InstallCommand());
        $this->add(new GenerateCommand());
        $this->add(new CleanCommand());
        $this->add(new RouterListCommand());
        $this->add(new RouterDispatchCommand());
    }

}

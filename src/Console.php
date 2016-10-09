<?php

namespace brendt\stitcher;

use brendt\stitcher\command\GenerateCommand;
use brendt\stitcher\command\SetupCommand;
use Symfony\Component\Console\Application;

class Console {

    protected $application;

    public function __construct() {
        $application = new Application();
        $application->setName('Stitcher Console');

        $application->add(new SetupCommand());
        $application->add(new GenerateCommand());


        $this->application = $application;
    }

    public function run() {
        $this->application->run();
    }

}

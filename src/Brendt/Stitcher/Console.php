<?php

namespace Brendt\Stitcher;

use Brendt\Stitcher\Command\CleanCommand;
use Brendt\Stitcher\Command\GenerateCommand;
use Brendt\Stitcher\Command\InstallCommand;
use Brendt\Stitcher\Command\RouterDispatchCommand;
use Brendt\Stitcher\Command\RouterListCommand;
use Symfony\Component\Console\Application;

class Console extends Application {

    public function __construct(string $configPath = './config.yml', array $defaultConfig = []) {
        parent::__construct('Stitcher Console');

        $this->add(new InstallCommand());
        $this->add(new GenerateCommand($configPath, $defaultConfig));
        $this->add(new CleanCommand());
        $this->add(new RouterListCommand($configPath, $defaultConfig));
        $this->add(new RouterDispatchCommand($configPath, $defaultConfig));
    }

}

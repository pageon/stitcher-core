<?php

namespace Stitcher\Task;

use Stitcher\File;
use Symfony\Component\Yaml\Yaml;

class Parse extends AbstractParse
{
    public function execute(): void
    {
        $parsedConfiguration = Yaml::parse(File::read($this->configurationFile));

        $pages = $this->parsePageConfiguration($parsedConfiguration);

        $this->renderPages($pages);

        $this->executeSubTasks();
    }
}

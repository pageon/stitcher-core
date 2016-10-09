<?php

namespace brendt\stitcher\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class SetupCommand extends Command {

    const ROOT = 'root';

    protected function configure() {
        $this->setName('site:setup')
            ->setDescription('Setup the src/ folder of your new website')
            ->setHelp("This command generated a new src/ folder with a basic setup.")
            ->addOption(self::ROOT, null, InputOption::VALUE_REQUIRED, 'Set the root directory in which to setup the src/ folder', './');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $root = $input->getOption(self::ROOT);

        if (!strpos('./', $root)) {
            $root = "./{$root}";
        }

        $fs = new Filesystem();

        if (!$fs->exists("{$root}/src")) {
            $fs->mkdir("{$root}/src");
        }
    }

}

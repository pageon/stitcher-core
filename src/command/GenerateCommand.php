<?php

namespace brendt\stitcher\command;

use brendt\stitcher\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use brendt\stitcher\Stitcher;

class GenerateCommand extends Command {

    protected function configure() {
        $this->setName('site:generate')
            ->setDescription('Generate the website')
            ->setHelp("This command generates the website based on the data in the src/ folder.");
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        Config::load();

        $stitcher = new Stitcher();

        $blanket = $stitcher->stitch();
        $stitcher->save($blanket);

        $output->writeln('Site successfully generated in <fg=green>public/</>.');

        return;
    }

}

<?php

namespace brendt\stitcher\command;

use brendt\stitcher\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use brendt\stitcher\Stitcher;

class GenerateCommand extends Command {

    const ROUTE = 'route';

    protected function configure() {
        $this->setName('site:generate')
            ->setDescription('Generate the website')
            ->setHelp("This command generates the website based on the data in the src/ folder.")
            ->addArgument(self::ROUTE, null, 'Specify a route to render');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        Config::load();
        $publicDir = Config::get('directories.public');
        $route = $input->getArgument(self::ROUTE);
        $stitcher = new Stitcher();

        $blanket = $stitcher->stitch($route);
        $stitcher->save($blanket);

        if ($route) {
            $output->writeln("<fg=green>{$route}</> successfully generated in <fg=green>{$publicDir}</>.");
        } else {
            $output->writeln("Site successfully generated in <fg=green>{$publicDir}</>.");
        }

        return;
    }

}

<?php

namespace brendt\stitcher\command;

use brendt\stitcher\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use brendt\stitcher\Stitcher;

class RoutesCommand extends Command {

    const FILTER = 'filter';

    protected function configure() {
        $this->setName('routes')
            ->setDescription('Show the available routes')
            ->setHelp("This command shows the available routes.")
            ->addArgument(self::FILTER, InputArgument::OPTIONAL, 'Specify a filter');
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
        $site = $stitcher->loadSite();
        $filter = $input->getArgument(self::FILTER);

        if ($filter) {
            $output->writeln("Available routes (filtered by <fg=green>{$filter}</>):\n");
        } else {
            $output->writeln("Available routes:\n");
        }

        foreach ($site as $route => $page) {
            if ($filter && strpos($route, $filter) === false) {
                continue;
            }

            $data = [];

            if (isset($page['data'])) {
                $data = array_keys($page['data']);
            }

            $line = "- <fg=green>{$route}</>: {$page['template']}.tpl";

            if (count($data)) {
                $line .= "\n\t";
                $line .= '$' . implode(', $', $data);
            }

            $output->writeln($line);
        }

        return;
    }

}

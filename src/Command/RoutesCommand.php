<?php

namespace Brendt\Stitcher\Command;

use Brendt\Stitcher\Stitcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RoutesCommand extends Command
{

    const FILTER = 'filter';

    protected function configure() {
        $this->setName('router:list')
            ->setDescription('Show the available routes')
            ->setHelp("This command shows the available routes.")
            ->addArgument(self::FILTER, InputArgument::OPTIONAL, 'Specify a filter');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $stitcher = Stitcher::create();
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

            if ($data) {
                $line .= "\n\t";
                $line .= '$' . implode(', $', $data);
            }

            $output->writeln($line);
        }
    }

}

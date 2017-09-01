<?php

namespace Brendt\Stitcher\Command;

use Brendt\Stitcher\Site\Page;
use Brendt\Stitcher\Stitcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RouterListCommand extends Command
{
    const FILTER = 'filter';

    private $stitcher;

    public function __construct(Stitcher $stitcher)
    {
        parent::__construct();

        $this->stitcher = $stitcher;
    }

    protected function configure()
    {
        $this->setName('router:list')
            ->setDescription('Show the available routes')
            ->setHelp("This command shows the available routes.")
            ->addArgument(self::FILTER, InputArgument::OPTIONAL, 'Specify a filter');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $site = $this->stitcher->loadSite();
        $filter = $input->getArgument(self::FILTER);

        $output->writeln("Found routes:");

        /**
         * @var string $route
         * @var Page   $page
         */
        foreach ($site as $route => $page) {
            if ($filter && strpos($page->getId(), $filter) === false) {
                continue;
            }

            $output->writeln("- <fg=green>{$page->getId()}</>: {$page->getTemplatePath()}.tpl");

            foreach ($page->getVariables() as $variable => $value) {
                $output->writeln("\t\${$variable}: {$value}");
            }
        }
    }
}

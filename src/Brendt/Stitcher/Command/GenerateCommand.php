<?php

namespace Brendt\Stitcher\Command;

use Brendt\Stitcher\Stitcher;
use Brendt\Stitcher\App;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends Command
{

    const ROUTE = 'route';

    /**
     * @var Stitcher
     */
    private $stitcher;

    public function __construct(string $configPath = './config.yml', array $defaultConfig = []) {
        parent::__construct();

        $this->stitcher = App::get('stitcher');
    }

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
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $route = $input->getArgument(self::ROUTE);
        $blanket = $this->stitcher->stitch($route);
        $this->stitcher->save($blanket);

        $publicDir = App::getParameter('directories.public');

        if ($route) {
            $output->writeln("<fg=green>{$route}</> successfully generated in <fg=green>{$publicDir}</>.");
        } else {
            $output->writeln("Site successfully generated in <fg=green>{$publicDir}</>.");
        }
    }

}

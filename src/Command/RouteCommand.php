<?php

namespace Brendt\Stitcher\Command;

use Brendt\Stitcher\Config;
use Brendt\Stitcher\Stitcher;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class RouteCommand extends Command {

    const URL = 'url';

    protected function configure() {
        $this->setName('router:dispatch')
            ->setDescription('Simulate routing of an URL')
            ->setHelp("Simulate routing of an URL.")
            ->addArgument(self::URL, InputArgument::REQUIRED);
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

        $url = $input->getArgument(self::URL);
        $routes = array_keys($site);
        $routeCollection = new RouteCollection();

        foreach ($routes as $route) {
            $routeCollection->add($route, new Route($route));
        }

        $matcher = new UrlMatcher($routeCollection, new RequestContext());
        $result = $matcher->match($url);

        $output->writeln("<fg=green>{$url}</> matches <fg=green>{$result['_route']}</>");

        return;
    }
}

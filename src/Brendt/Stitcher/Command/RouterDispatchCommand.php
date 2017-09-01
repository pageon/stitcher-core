<?php

namespace Brendt\Stitcher\Command;

use Brendt\Stitcher\Stitcher;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class RouterDispatchCommand extends Command
{
    const URL = 'url';

    private $stitcher;

    public function __construct(Stitcher $stitcher)
    {
        parent::__construct();

        $this->stitcher = $stitcher;
    }

    protected function configure()
    {
        $this->setName('router:dispatch')
            ->setDescription('Simulate routing of an URL')
            ->setHelp("Simulate routing of an URL.")
            ->addArgument(self::URL, InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $site = $this->stitcher->loadSite();

        $url = $input->getArgument(self::URL);
        $routes = [];
        foreach ($site as $page) {
            $routes[] = $page->getId();
        }
        $routeCollection = new RouteCollection();

        foreach ($routes as $route) {
            $routeCollection->add($route, new Route($route));
        }

        $matcher = new UrlMatcher($routeCollection, new RequestContext());
        $result = $matcher->match($url);

        $output->writeln("<fg=green>{$url}</> matches <fg=green>{$result['_route']}</>");
    }
}

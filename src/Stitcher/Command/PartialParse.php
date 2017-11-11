<?php

namespace Stitcher\Command;

use Stitcher\File;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Yaml\Yaml;

class PartialParse extends AbstractParse
{
    private $filter;

    public function setFilter(string $filter): PartialParse
    {
        $this->filter = $filter;

        return $this;
    }

    public function execute(): void
    {
        $parsedConfiguration = (array) Yaml::parse(File::read($this->configurationFile));

        $routeCollection = $this->createRouteCollection($parsedConfiguration);
        $matcher = new UrlMatcher($routeCollection, new RequestContext());
        $matchingRoute = $matcher->match($this->filter);

        $filteredConfiguration = array_filter($parsedConfiguration, function ($key) use ($matchingRoute) {
            return $key === $matchingRoute['_route'];
        }, ARRAY_FILTER_USE_KEY);

        $pages = $this->parsePageConfiguration($filteredConfiguration);

        $this->renderPages($pages);
    }

    private function createRouteCollection(array $configuration): RouteCollection
    {
        $routeCollection = new RouteCollection();

        foreach ($configuration as $id => $pageConfiguration) {
            $routeCollection->add($id, new Route($id));
        }

        return $routeCollection;
    }
}

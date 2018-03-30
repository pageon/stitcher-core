<?php

namespace Stitcher\Task;

use Stitcher\Exception\InvalidConfiguration;
use Stitcher\File;
use Stitcher\Page\Adapter\CollectionAdapter;
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
        $parsedConfiguration = $this->getParsedConfiguration();

        $routeCollection = $this->createRouteCollection($parsedConfiguration);

        $matcher = new UrlMatcher($routeCollection, new RequestContext());

        $matchingRoute = $matcher->match($this->filter);

        CollectionAdapter::setFilterId($matchingRoute['id'] ?? null);

        $filteredConfiguration = array_filter(
            $parsedConfiguration,
            function ($key) use ($matchingRoute) {
                return $key === $matchingRoute['_route'];
            }, ARRAY_FILTER_USE_KEY
        );

        $pages = $this->parsePageConfiguration($filteredConfiguration);

        $this->renderPages($pages);

        $this->executeSubTasks();
    }

    private function createRouteCollection(array $configuration): RouteCollection
    {
        $routeCollection = new RouteCollection();

        foreach ($configuration as $id => $pageConfiguration) {
            $routeCollection->add($id, new Route($id));
        }

        return $routeCollection;
    }

    private function getParsedConfiguration(): array
    {
        $configurationFile = File::read($this->configurationFile);

        if (! $configurationFile) {
            throw InvalidConfiguration::siteConfigurationFileNotFound();
        }

        return (array) Yaml::parse($configurationFile);
    }
}

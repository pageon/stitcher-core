<?php

namespace brendt\stitcher\controller;

use brendt\stitcher\Config;
use brendt\stitcher\exception\StitcherException;
use brendt\stitcher\Stitcher;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class DevController {

    public function __construct($configPath = './') {
        Config::load($configPath);

        $this->stitcher = new Stitcher();
    }

    public function run() {
        $url = $_SERVER['REQUEST_URI'];

        $site = $this->stitcher->loadSite();
        $routes = array_keys($site);
        $routeCollection = new RouteCollection();

        foreach ($routes as $route) {
            $routeCollection->add($route, new Route($route));
        }

        try {
            $matcher = new UrlMatcher($routeCollection, new RequestContext());
            $routeResult = $matcher->match($url);
            $route = $routeResult['_route'];

            $blanket = $this->stitcher->stitch($route);

            if (isset($blanket[$route])) {
                echo $blanket[$route];

                return;
            }

            if (isset($blanket[$url])) {
                echo $blanket[$url];

                return;
            }

            throw new ResourceNotFoundException();
        } catch (StitcherException $e) {
            echo $e->getMessage();
        }catch (ResourceNotFoundException $e) {
            echo "404";
        }

        return;
    }

}

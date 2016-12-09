<?php

namespace brendt\stitcher\controller;

use brendt\stitcher\Config;
use brendt\stitcher\exception\StitcherException;
use brendt\stitcher\factory\AdapterFactory;
use brendt\stitcher\Stitcher;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * The developer controller is used to render pages on the fly (on an HTTP request). This controller enables a
 * developer make code changes and see those changes real-time without re-compiling the whole site.
 */
class DevController {

    /**
     * @var Stitcher
     */
    protected $stitcher;

    /**
     * Create a new developer controller with optional configuration path and -file.
     *
     * @param string $path
     * @param string $name
     */
    public function __construct($path = './', $name = 'config.dev.yml') {
        Config::load($path, $name);

        $this->stitcher = new Stitcher();
    }

    /**
     * Run the developer controller.
     *
     * This function will read the request URL and dispatch the according route.
     */
    public function run() {
        $request = explode('?', $_SERVER['REQUEST_URI']);
        $url = reset($request);

        $routeCollection = new RouteCollection();
        $site = $this->stitcher->loadSite();

        foreach ($site as $page) {
            $route = $page->getId();

            $routeCollection->add($route, new Route($route));

            if ($page->getAdapterConfig(AdapterFactory::PAGINATION_ADAPTER)) {
                $paginationRoute = $route . '/page-{page}';
                $routeCollection->add($paginationRoute, new Route($paginationRoute));
            }
        }

        try {
            $matcher = new UrlMatcher($routeCollection, new RequestContext());
            $routeResult = $matcher->match($url);
            $route = $routeResult['_route'];

            $id = isset($routeResult['id']) ? $routeResult['id'] : null;

            if (isset($routeResult['page'])) {
                $route = str_replace('/page-{page}', '', $route);
                $id = $routeResult['page'];
            }

            $blanket = $this->stitcher->stitch($route, $id);

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
        } catch (ResourceNotFoundException $e) {
            echo "404";
        }

        return;
    }

}

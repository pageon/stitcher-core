<?php

namespace Brendt\Stitcher\Application;

use Brendt\Stitcher\Exception\StitcherException;
use Brendt\Stitcher\Factory\AdapterFactory;
use Brendt\Stitcher\Stitcher;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * The developer controller is used to render pages on the fly (on an HTTP request). This controller enables a
 * developer make code changes and see those changes real-time without re-compiling the whole site.
 */
class DevController
{
    const ENVIRONMENT = 'development';

    protected $stitcher;

    public function __construct(Stitcher $stitcher)
    {
        $this->stitcher = $stitcher;
    }

    /**
     * Run the developer controller. This function will read the request URL and dispatch the according route.
     *
     * @param string $url
     *
     * @return string
     */
    public function run($url = null)
    {
        if ($url === null) {
            $request = explode('?', $_SERVER['REQUEST_URI']);
            $url = reset($request);
        }

        try {
            return $this->getPage($url);
        } catch (StitcherException $e) {
            return $e->getMessage();
        } catch (ResourceNotFoundException $e) {
            return "404";
        }
    }

    protected function createRouteCollection()
    {
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

        return $routeCollection;
    }

    protected function getPage($url)
    {
        $routeCollection = $this->createRouteCollection();
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
            return $blanket[$route];
        }

        if (isset($blanket[$url])) {
            return $blanket[$url];
        }

        throw new ResourceNotFoundException();
    }
}

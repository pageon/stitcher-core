<?php

namespace Stitcher\Application;

use FastRoute\Dispatcher;
use FastRoute\Dispatcher\GroupCountBased;
use FastRoute\RouteCollector;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Stitcher\App;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class Router
{
    /** @var \FastRoute\RouteCollector */
    protected $routeCollector;

    /** @var array */
    protected $redirects = [];

    public function __construct(RouteCollector $routeCollector)
    {
        $this->routeCollector = $routeCollector;
    }

    public function get(string $url, string $controller): Router
    {
        $this->routeCollector->addRoute('GET', $url, [$controller, 'handle']);

        return $this;
    }

    public function put(string $url, string $controller): Router
    {
        $this->routeCollector->addRoute('PUT', $url, [$controller, 'handle']);

        return $this;
    }

    public function post(string $url, string $controller): Router
    {
        $this->routeCollector->addRoute('POST', $url, [$controller, 'handle']);

        return $this;
    }

    public function patch(string $url, string $controller): Router
    {
        $this->routeCollector->addRoute('PATCH', $url, [$controller, 'handle']);

        return $this;
    }

    public function delete(string $url, string $controller): Router
    {
        $this->routeCollector->addRoute('DELETE', $url, [$controller, 'handle']);

        return $this;
    }

    public function redirect(string $url, string $targetUrl): Router
    {
        $this->redirects[$url] = $targetUrl;

        return $this;
    }

    public function getRedirectForUrl(string $url): ?string
    {
        return $this->redirects[$url] ?? null;
    }

    public function dispatch(Request $request): ?Response
    {
        $dispatcher = new GroupCountBased($this->routeCollector->getData());

        $routeInfo = $dispatcher->dispatch(
            $request->getMethod(),
            $request->getUri()->getPath()
        );

        if ($routeInfo[0] !== Dispatcher::FOUND) {
            return null;
        }

        $handler = $this->resolveHandler($routeInfo[1]);
        $parameters = $routeInfo[2];

        return call_user_func_array(
            $handler,
            array_merge($parameters, [$request])
        );
    }

    protected function resolveHandler(array $callback): array
    {
        $className = $callback[0];

        try {
            $handler = App::get($className);
        } catch (ServiceNotFoundException $e) {
            $handler = new $className();
        }

        $callback[0] = $handler;

        return $callback;
    }
}

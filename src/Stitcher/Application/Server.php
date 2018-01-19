<?php

namespace Stitcher\Application;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Stitcher\Exception\Http;

abstract class Server
{
    /** @var Router */
    protected $router;

    /** @var Request */
    protected $request;

    public function setRouter(Router $router): Server
    {
        $this->router = $router;

        return $this;
    }

    public function run(): string
    {
        $response = $this->handleStaticRoute();

        if (!$response) {
            $response = $this->handleDynamicRoute();
        }

        if (!$response) {
            throw Http::notFound($this->getCurrentPath());
        }

        return $response->getBody()->getContents();
    }

    protected function getRequest(): Request
    {
        if (!$this->request) {
            $this->request = ServerRequest::fromGlobals();
        }

        return $this->request;
    }

    protected function getCurrentPath(): string
    {
        $path = $this->getRequest()->getUri()->getPath();

        return $path === '' ? '/' : $path;
    }

    abstract protected function handleStaticRoute(): ?Response;

    protected function handleDynamicRoute(): ?Response
    {
        if (! $this->router) {
            return null;
        }

        return $this->router->dispatch($this->getRequest());
    }
}

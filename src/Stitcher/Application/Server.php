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

    public function run(): string
    {
        if ($html = $this->handleStaticRoute()) {
            return $html;
        }

        if ($response = $this->handleDynamicRoute()) {
            return $response->getBody()->getContents();
        }

        throw Http::notFound($this->getCurrentPath());
    }

    public function setRouter(Router $router): Server
    {
        $this->router = $router;

        return $this;
    }

    public function getRequest(): Request
    {
        if (!$this->request) {
            $this->request = ServerRequest::fromGlobals();
        }

        return $this->request;
    }

    public function getCurrentPath(): ?string
    {
        $path = $this->getRequest()->getUri()->getPath();

        return $path === '' ? '/' : $path;
    }

    abstract protected function handleStaticRoute(): ?string;

    protected function handleDynamicRoute(): ?Response
    {
        if (! $this->router) {
            return null;
        }

        return $this->router->dispatch($this->getRequest());
    }
}

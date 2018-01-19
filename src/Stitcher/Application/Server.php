<?php

namespace Stitcher\Application;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;

abstract class Server
{
    /** @var Router */
    protected $router;

    abstract public function run(): string;

    public function setRouter(Router $router): Server
    {
        $this->router = $router;

        return $this;
    }

    public function createRequest(): Request
    {
        /** @var Request $request */
        $request = ServerRequest::fromGlobals();

        return $request;
    }

    protected function handleDynamicRoute(): ?Response
    {
        if (! $this->router) {
            return null;
        }

        return $this->router->dispatch($this->createRequest());
    }
}

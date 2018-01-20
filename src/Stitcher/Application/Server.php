<?php

namespace Stitcher\Application;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Stitcher\Exception\Http;
use Stitcher\Exception\StitcherException;

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
        $response = $this->createResponse();

        return $this->handleResponse($response);
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

    protected function createResponse(): Response
    {
        try {
            $response = $this->handleStaticRoute();

            if (!$response) {
                $response = $this->handleDynamicRoute();
            }
        } catch (StitcherException $e) {
            $response = $this->responseFromException($e);
        }

        if (!$response) {
            $response = $this->responseFromException(
                Http::notFound(
                    $this->getCurrentPath()
                )
            );
        }

        return $response;
    }

    protected function responseFromException(StitcherException $e): Response
    {
        $statusCode = 500;

        if ($e instanceof Http) {
            $statusCode = $e->statusCode();
        }

        return new Response($statusCode, [], $e->title());
    }

    protected function handleResponse(Response $response): string
    {
        http_response_code($response->getStatusCode());

        return $response->getBody()->getContents();
    }
}

<?php

namespace Stitcher\Application;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Parsedown;
use Stitcher\Exception\Http;
use Stitcher\Exception\StitcherException;

abstract class Server
{
    /** @var Router */
    protected $router;

    /** @var Request */
    protected $request;

    /** @var Parsedown */
    protected $markdownParser;

    public function setRouter(Router $router): Server
    {
        $this->router = $router;

        return $this;
    }

    public function setMarkdownParser(Parsedown $markdownParser): Server
    {
        $this->markdownParser = $markdownParser;

        return $this;
    }

    public function run(): string
    {
        $response = $this->createResponse();

        return $this->handleResponse($response);
    }

    protected function getRequest(): Request
    {
        if (! $this->request) {
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

            if (! $response) {
                $response = $this->handleDynamicRoute();
            }
        } catch (StitcherException $e) {
            $response = $this->responseFromException($e);
        }

        return $response ?? $this->responseFromException(
                Http::notFound(
                    $this->getCurrentPath()
                )
            );
    }

    protected function responseFromException(StitcherException $e): Response
    {
        $statusCode = $e instanceof Http ? $e->statusCode() : 500;

        $responseBody = file_get_contents(__DIR__ . '/../../static/exception.html');

        $responseBody = str_replace(
            '{{ title }}',
            $this->markdownParser->parse($e->title()),
            $responseBody
        );

        $responseBody = str_replace(
            '{{ body }}',
            $this->markdownParser->parse($e->body()),
            $responseBody
        );

        return new Response($statusCode, [], $responseBody);
    }

    protected function handleResponse(Response $response): string
    {
        http_response_code($response->getStatusCode());

        return $response->getBody()->getContents();
    }
}

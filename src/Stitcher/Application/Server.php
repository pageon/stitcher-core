<?php

namespace Stitcher\Application;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Pageon\Http\HeaderContainer;
use Stitcher\Exception\ErrorHandler;
use Stitcher\Exception\Http;
use Stitcher\Exception\StitcherException;

abstract class Server
{
    /** @var Router */
    protected $router;

    /** @var Request */
    protected $request;

    /** @var HeaderContainer */
    protected $headerContainer;

    /** @var \Stitcher\Exception\ErrorHandler */
    protected $errorHandler;

    abstract protected function handleStaticRoute(): ?Response;

    public function setRouter(Router $router): Server
    {
        $this->router = $router;

        return $this;
    }

    public function setHeaderContainer(HeaderContainer $headerContainer): Server
    {
        $this->headerContainer = $headerContainer;

        return $this;
    }

    public function setErrorHandler(ErrorHandler $errorHandler): Server
    {
        $this->errorHandler = $errorHandler;

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

    protected function handleDynamicRoute(): ?Response
    {
        if (! $this->router) {
            return null;
        }

        return $this->router->dispatch($this->getRequest());
    }

    protected function createResponse(): Response
    {
        if (
            $this->router
            && $redirectTo = $this->router->getRedirectForUrl($this->getCurrentPath())
        ) {
            return $this->createRedirectResponse($redirectTo);
        }

        try {
            $response = $this->handleStaticRoute();

            if (! $response) {
                $response = $this->handleDynamicRoute();
            }
        } catch (StitcherException $e) {
            $response = $this->createErrorResponse($e);
        }

        return $response ?? $this->createErrorResponse(
                Http::notFound(
                    $this->getCurrentPath()
                )
            );
    }

    protected function createRedirectResponse(string $targetUrl): Response
    {
        return new Response(301, ["Location: {$targetUrl}"]);
    }

    protected function createErrorResponse(StitcherException $exception): Response
    {
        $statusCode = $exception instanceof Http ? $exception->statusCode() : 500;

        $responseBody = $this->errorHandler->handle($statusCode, $exception);

        return new Response($statusCode, [], $responseBody);
    }

    protected function handleResponse(Response $response): string
    {
        foreach ($response->getHeaders() as $name => $headers) {
            header($name . ':'. implode(', ', $headers));
        }

        if ($this->headerContainer) {
            foreach ($this->headerContainer as $header) {
                header((string) $header);
            }
        }

        http_response_code($response->getStatusCode());

        return $response->getBody()->getContents();
    }
}

<?php

namespace Stitcher\Exception;

use Pageon\Lib\Markdown\MarkdownParser;
use Stitcher\Renderer\RendererFactory;

class ErrorHandler
{
    /** @var \Stitcher\Renderer\Renderer */
    private $renderer;

    /** @var \Pageon\Lib\Markdown\MarkdownParser */
    private $markdownParser;

    /** @var array */
    private $errorPages;

    /** @var string */
    private $defaultErrorPage;

    public function __construct(
        RendererFactory $rendererFactory,
        MarkdownParser $markdownParser,
        array $errorPages = []
    ) {
        $this->renderer = $rendererFactory->create();
        $this->markdownParser = $markdownParser;
        $this->errorPages = $errorPages;
        $this->defaultErrorPage = __DIR__ . '/../../static/exception.html';
    }

    public function handle(int $statusCode, ?StitcherException $exception = null): string
    {
        $template = $this->errorPages[$statusCode] ?? null;

        if (! $template) {
            return $this->handleStaticError($exception);
        }

        return $this->renderer->renderTemplate($template, [
            'error_title' => $this->markdownParser->parse($exception->title()),
            'error_body' => $this->markdownParser->parse($exception->body()),
        ]);
    }

    private function handleStaticError(?StitcherException $exception): string
    {
        $html = file_get_contents($this->defaultErrorPage);

        if (! $exception) {
            return $html;
        }

        $html = str_replace(
            ['{{ title }}', '{{ body }}'],
            [$this->markdownParser->parse($exception->title()), $this->markdownParser->parse($exception->body())],
            $html
        );

        return $html;
    }
}

<?php

namespace Stitcher\Page;

use Stitcher\Renderer;

class PageRenderer
{
    private $renderer;

    public function __construct(Renderer $renderer)
    {
        $this->renderer = $renderer;
    }

    public static function make(Renderer $renderer): PageRenderer
    {
        return new self($renderer);
    }

    public function render(Page $page): string
    {
        $variables = $page->variables();
        $variables['_meta'] = $page->meta();

        return $this->renderer->renderTemplate($page->template(), $variables);
    }
}

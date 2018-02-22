<?php

namespace Stitcher\Renderer\Extension;

use Stitcher\Page\Page as SitePage;
use Stitcher\Page\PageParser;
use Stitcher\Renderer\Extension;

class Page implements Extension
{
    private $pageParser;

    public function __construct(PageParser $pageParser)
    {
        $this->pageParser = $pageParser;
    }

    public function name(): string
    {
        return 'page';
    }

    public function isActive(string $part): bool
    {
        return $this->isCurrent($part);
    }

    public function isCurrent(string $part): bool
    {
        $currentPage = $this->pageParser->getCurrentPage();

        if (!$currentPage) {
            return false;
        }

        return strpos($currentPage->id(), $part) !== false;
    }

    public function current(): ?SitePage
    {
        return $this->pageParser->getCurrentPage();
    }
}

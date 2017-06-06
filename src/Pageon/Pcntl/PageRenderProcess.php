<?php

namespace Pageon\Pcntl;

use Brendt\Stitcher\Event\Event;
use Brendt\Stitcher\Parser\Site\PageParser;
use Brendt\Stitcher\Parser\Site\SiteParser;
use Brendt\Stitcher\Site\Page;
use Symfony\Component\Filesystem\Filesystem;

class PageRenderProcess extends Process
{
    /**
     * @var PageParser
     */
    private $pageParser;

    /**
     * @var Page
     */
    private $page;

    /**
     * @var null
     */
    private $filterValue;

    /**
     * @var string
     */
    private $publicDir;

    /**
     * @var bool
     */
    private $async = false;

    public function __construct(PageParser $pageParser, Page $page, string $publicDir, string $filterValue = null) {
        parent::__construct($page->getId());

        $this->pageParser = $pageParser;
        $this->page = $page;
        $this->filterValue = $filterValue;
        $this->publicDir = $publicDir;
    }

    public function setAsync(bool $async = true) {
        $this->async = $async;
    }

    public function execute() {
        $this->pageParser->validate($this->page);
        $pages = $this->pageParser->parseAdapters($this->page, $this->filterValue);
        $blanket = [];
        $fs = new Filesystem();

        /** @var Page $page */
        foreach ($pages as $page) {
            $path = $page->getId();

            if ($path === '/') {
                $path = 'index';
            }

            $fs->dumpFile($this->publicDir . "/{$path}.html", $this->pageParser->parsePage($page));

            $blanket[$page->getId()] = $this->pageParser->parsePage($page);
        }

        if ($this->async) {
            return Event::create(['pageId' => $this->page->getId()], SiteParser::EVENT_PAGE_PARSED);
        } else {
            return $blanket;
        }
    }
}

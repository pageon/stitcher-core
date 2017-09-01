<?php

namespace Pageon\Pcntl;

use Brendt\Stitcher\Application\DevController;
use Brendt\Stitcher\Parser\Site\PageParser;
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

    /**
     * @var string
     */
    private $environment = null;

    public function __construct(PageParser $pageParser, Page $page, string $publicDir, string $filterValue = null) {
        $this->name = $page->getId();
        $this->pageParser = $pageParser;
        $this->page = $page;
        $this->filterValue = $filterValue;
        $this->publicDir = $publicDir;
    }

    public function setAsync(bool $async = true) {
        $this->async = $async;
    }

    public function setEnvironment($environment) {
        $this->environment = $environment;
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

            if ($this->environment !== DevController::ENVIRONMENT) {
                $fs->dumpFile($this->publicDir . "/{$path}.html", $this->pageParser->parsePage($page));
            }

            $blanket[$page->getId()] = $this->pageParser->parsePage($page);
        }

        if ($this->async) {
            return array_keys($blanket);
        } else {
            return $blanket;
        }
    }
}

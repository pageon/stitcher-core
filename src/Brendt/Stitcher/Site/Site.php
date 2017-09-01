<?php

namespace Brendt\Stitcher\Site;

use \Iterator;

class Site implements Iterator
{
    /** @var Page[] */
    private $pages;

    /** @var int */
    private $position = 0;

    public function __construct()
    {
        $this->position = 0;
    }

    public function getPages() : array
    {
        return $this->pages;
    }

    public function setPages(array $pages)
    {
        $this->pages = $pages;
        $this->rewind();
    }

    public function addPage(Page $page)
    {
        $this->pages[] = $page;
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function current()
    {
        return $this->pages[$this->position];
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
    }

    public function valid()
    {
        return isset($this->pages[$this->position]);
    }
}

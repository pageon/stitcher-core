<?php

namespace Brendt\Stitcher\Adapter;

use Brendt\Stitcher\Site\Page;

/**
 * An adapter is used to take one page and transform its config and variables to one or more other pages.
 */
interface Adapter
{

    /**
     * @param Page|Page[]       $pages
     * @param string|null $filter
     *
     * @return Page[]
     */
    public function transform($pages, $filter = null) : array;

    /**
     * @param Page $page
     * @param string|null $filter
     *
     * @return Page[]
     */
    public function transformPage(Page $page, $filter = null) : array;

}

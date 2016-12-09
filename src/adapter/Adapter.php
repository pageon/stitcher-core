<?php

namespace brendt\stitcher\adapter;

use brendt\stitcher\site\Page;

/**
 * An adapter is used to take one page and transform its config and variables to one or more other pages.
 */
interface Adapter {

    /**
     * @param Page        $page
     * @param string|null $filter
     *
     * @return Page[]
     */
    public function transform(Page $page, $filter = null);

}

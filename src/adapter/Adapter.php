<?php

namespace brendt\stitcher\adapter;

use brendt\stitcher\site\Page;

interface Adapter {

    public function transform(Page $page, $filter = null);

}

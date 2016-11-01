<?php

namespace brendt\stitcher\adapter;

use brendt\stitcher\element\Page;

interface Adapter {

    public function transform(Page $page);

}

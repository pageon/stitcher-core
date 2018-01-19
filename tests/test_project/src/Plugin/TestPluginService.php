<?php

namespace Stitcher\Test\Plugin;

class TestPluginService
{
    public $item;

    public function __construct($item)
    {
        $this->item = $item;
    }
}

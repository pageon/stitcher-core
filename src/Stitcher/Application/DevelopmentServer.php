<?php

namespace Stitcher\Application;

class DevelopmentServer
{
    protected $html;

    public function __construct()
    {

    }

    public function run(): string
    {
        return $this->html;
    }
}

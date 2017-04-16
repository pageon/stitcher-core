<?php

namespace Brendt\Stitcher\Site\Http;

use Brendt\Stitcher\Site\Page;

interface HeaderCompiler
{
    public function compilePage(Page $page) : void;
}

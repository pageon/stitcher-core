<?php

namespace Stitcher\Renderer\Extension;

use Leafo\ScssPhp\Compiler as Sass;
use Stitcher\Renderer\Extension;

class Css implements Extension
{
    private $sass;

    public function __construct(Sass $sass)
    {
        $this->sass = $sass;
    }

    public function handle(string $src)
    {
//        dd($src);
    }
}

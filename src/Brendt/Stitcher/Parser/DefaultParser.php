<?php

namespace Brendt\Stitcher\Parser;

class DefaultParser implements Parser
{
    public function parse($path)
    {
        return $path;
    }
}

<?php

namespace Brendt\Stitcher\Parser;

/**
 * A parser is used to parse file data, eg. a YAML or image file, into usable data within the Stitcher application.
 */
interface Parser
{
    public function parse($path);
}

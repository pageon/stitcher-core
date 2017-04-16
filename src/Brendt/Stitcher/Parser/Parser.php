<?php

namespace Brendt\Stitcher\Parser;

/**
 * A parser is used to parse file data, eg. a YAML or image file, into usable data within the Stitcher application.
 */
interface Parser
{

    /**
     * Parse a path into usable data.
     *
     * @param $path
     *
     * @return mixed
     */
    public function parse($path);

}

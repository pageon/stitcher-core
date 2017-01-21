<?php

namespace brendt\stitcher\parser;

class DefaultParser implements Parser {

    /**
     * Parse a path into usable data.
     *
     * @param $path
     *
     * @return mixed
     */
    public function parse($path) {
        return $path;
    }
}

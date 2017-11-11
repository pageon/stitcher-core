<?php

namespace Stitcher\Variable;

class DefaultVariable extends AbstractVariable
{
    public static function make($value): DefaultVariable
    {
        return new self($value);
    }

    public function parse(): AbstractVariable
    {
        $this->parsed = $this->unparsed;

        return $this;
    }
}

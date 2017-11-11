<?php

namespace Stitcher\Variable;

use Stitcher\File;

class JsonVariable extends AbstractVariable
{
    public static function make(string $value): JsonVariable
    {
        return new self($value);
    }

    public function parse(): AbstractVariable
    {
        $this->parsed = json_decode(File::read($this->unparsed), true);

        return $this;
    }
}

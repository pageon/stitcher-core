<?php

namespace Stitcher\Variable;

use Stitcher\File;

class HtmlVariable extends AbstractVariable
{
    public function parse(): AbstractVariable
    {
        $this->parsed = File::read($this->unparsed);

        return $this;
    }
}

<?php

namespace Stitcher;

interface Parseable
{
    public function unparsed();

    public function parsed();

    public function parse();
}

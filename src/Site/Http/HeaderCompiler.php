<?php

namespace Brendt\Stitcher\Site\Http;

interface HeaderCompiler
{
    public function compile(array $headers);
}

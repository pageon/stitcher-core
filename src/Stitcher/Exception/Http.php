<?php

namespace Stitcher\Exception;

use Exception;

class Http extends Exception
{
    public static function notFound(string $uri): Http
    {
        return new self("{$uri} was not found");
    }
}

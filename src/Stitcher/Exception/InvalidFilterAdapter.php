<?php

namespace Stitcher\Exception;

class InvalidFilterAdapter extends StitcherException
{
    public static function create(): InvalidFilterAdapter
    {
        return new self(
            "The `filter` adapter requires following configuration: `field`: `filter`"
        );
    }
}

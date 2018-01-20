<?php

namespace Stitcher\Exception;

class Http extends StitcherException
{
    protected $statusCode;

    public function __construct(string $title, string $body, int $statusCode = 500)
    {
        parent::__construct($title, $body);

        $this->statusCode = $statusCode;
    }

    public static function notFound(string $uri): Http
    {
        return new self("{$uri} was not found.", '', 404);
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }
}

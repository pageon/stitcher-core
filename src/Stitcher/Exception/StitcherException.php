<?php

namespace Stitcher\Exception;

use Exception;

class StitcherException extends Exception
{
    protected $title;
    protected $body;

    public function __construct(string $title, ?string $body = null)
    {
        parent::__construct($title);

        $this->title = $title;
        $this->body = $body;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function body(): ?string
    {
        return $this->body;
    }
}

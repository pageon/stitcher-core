<?php

namespace Pageon\Html;

class Source
{
    private $url;
    private $content;

    public function __construct(string $url, string $content)
    {
        $this->url = $url;
        $this->content = $content;
    }

    public function url(): string
    {
        return $this->url;
    }

    public function content(): string
    {
        return $this->content;
    }
}

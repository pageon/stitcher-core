<?php

namespace Pageon\Http;

class Header
{
    private $name;

    private $content;

    public function __construct(string $name, ?string $content = null)
    {
        $this->name = $name;
        $this->content = $content;
    }

    public static function make(string $name, ?string $content = null): Header
    {
        return new self($name, $content);
    }

    public function __toString(): string
    {
        return "{$this->name}: {$this->content}";
    }
}

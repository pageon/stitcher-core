<?php

namespace Brendt\Stitcher\Site\Http;

class Header
{
    private $name;
    private $content;

    public static function create(string $name, string $content) : Header
    {
        return new self($name, $content);
    }

    public static function link(string $content) : Header
    {
        return new self('Link', $content);
    }

    public function __construct(string $name, string $content)
    {
        $this->name = $name;
        $this->content = $content;
    }

    public function getHtaccessHeader() : string
    {
        return "{$this->name} {$this->content}";
    }

    public function __toString() : string
    {
        return "{$this->name}: {$this->content}";
    }
}

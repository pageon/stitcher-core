<?php

namespace Pageon\Http;

use Iterator;

class HeaderContainer implements Iterator
{
    private $position;

    private $headers = [];

    public function add(Header $header): HeaderContainer
    {
        $this->position = 0;
        $this->headers[] = $header;

        return $this;
    }

    public function current(): Header
    {
        return $this->headers[$this->position];
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return isset($this->headers[$this->position]);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }
}

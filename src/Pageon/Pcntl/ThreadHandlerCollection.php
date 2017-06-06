<?php

namespace Pageon\Pcntl;

use ArrayAccess;
use InvalidArgumentException;
use Iterator;

class ThreadHandlerCollection implements Iterator, ArrayAccess
{
    private $position;

    private $array = [];

    public function __construct() {
        $this->position = 0;
    }

    public function current() {
        return $this->array[$this->position];
    }

    public function next() {
        ++$this->position;
    }

    public function key() {
        return $this->position;
    }

    public function valid() {
        return isset($this->array[$this->position]);
    }

    public function rewind() {
        $this->position = 0;
    }

    public function offsetExists($offset) {
        return isset($this->array[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->array[$offset]) ? $this->array[$offset] : null;
    }

    public function offsetSet($offset, $value) {
        if (!$value instanceof ThreadHandler) {
            throw new InvalidArgumentException("value must be instance of ThreadHandler.");
        }

        if (is_null($offset)) {
            $this->array[] = $value;
        } else {
            $this->array[$offset] = $value;
        }
    }

    public function offsetUnset($offset) {
        unset($this->array[$offset]);
    }
}

<?php

namespace Pageon\Pcntl;

use ArrayAccess;
use InvalidArgumentException;
use Iterator;

/**
 * @codeCoverageIgnore
 */
class ProcessCollection implements Iterator, ArrayAccess
{
    private $position;

    private $array = [];

    public function __construct() {
        $this->position = 0;
    }

    public function isEmpty() : bool {
        return count($this->array) === 0;
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
        if (!$value instanceof Process) {
            throw new InvalidArgumentException("value must be instance of Process.");
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

    public function toArray() {
        return $this->array;
    }
}

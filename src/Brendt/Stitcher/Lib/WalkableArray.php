<?php

namespace Brendt\Stitcher\Lib;

class WalkableArray implements \ArrayAccess, \Iterator
{
    const TOKEN_NESTING = '.';

    private $array = [];
    private $position = 0;

    public function __construct(array $array = [])
    {
        $this->array = $array;
    }

    public static function fromArray(array $array) : WalkableArray
    {
        return new self($array);
    }

    public function get($pathParts)
    {
        if (!is_array($pathParts)) {
            $pathParts = explode(self::TOKEN_NESTING, $pathParts);
        }

        $element = $this->array;

        foreach ($pathParts as $key => $pathPart) {
            if (!isset($element[$pathPart])) {
                return null;
            }

            $element = $element[$pathPart];
        }

        return $element;
    }

    public function set($pathParts, $value, array $callbackArguments = []) : WalkableArray
    {
        if (!is_array($pathParts)) {
            $pathParts = explode(self::TOKEN_NESTING, $pathParts);
        }

        end($pathParts);
        $lastPathKey = key($pathParts);
        reset($pathParts);

        $element = &$this->array;

        foreach ($pathParts as $key => $pathPart) {
            if (!isset($element[$pathPart])) {
                $element[$pathPart] = $key === $lastPathKey ? null : [];
            }

            $element = &$element[$pathPart];
        }

        if ($value instanceof \Closure) {
            $element = call_user_func_array($value, $callbackArguments);
        } else {
            $element = $value;
        }

        unset($element);

        return $this;
    }

    public function toArray() : array
    {
        $array = $this->array;
        reset($array);

        return $array;
    }

    public function current()
    {
        return current($this->array);
    }

    public function offsetGet($offset)
    {
        return isset($this->array[$offset]) ? $this->array[$offset] : $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->array[] = $value;
        } elseif (isset($this->array[$offset])) {
            $this->array[$offset] = $value;
        } else {
            $this->set($offset, $value);
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->array[$offset]) || $this->get($offset) !== null;
    }

    public function offsetUnset($offset)
    {
        unset($this->array[$offset]);
    }

    public function next()
    {
        ++$this->position;
    }

    public function key()
    {
        return $this->position;
    }

    public function valid()
    {
        return isset($this->array[$this->position]);
    }

    public function rewind()
    {
        $this->position = 0;
    }
}

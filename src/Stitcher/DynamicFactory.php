<?php

namespace Stitcher;

abstract class DynamicFactory
{
    private $rules = [];

    public function setRule(string $class, callable $callback): DynamicFactory
    {
        $this->rules[$class] = $callback;

        return $this;
    }

    public function removeRule(string $class): DynamicFactory
    {
        if (isset($this->rules[$class])) {
            unset($this->rules[$class]);
        }

        return $this;
    }

    protected function getRules(): array
    {
        return $this->rules;
    }
}

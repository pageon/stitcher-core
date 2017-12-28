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

    protected function getRules(): array
    {
        return $this->rules;
    }
}

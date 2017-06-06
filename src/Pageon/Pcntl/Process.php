<?php

namespace Pageon\Pcntl;

abstract class Process
{
    protected $name;

    public function __construct(string $name) {
        $this->name = $name;
    }

    public function getName() : string {
        return $this->name;
    }

    public abstract function execute();
}

<?php

namespace Pageon\Pcntl;

abstract class Process
{
    protected $pid;

    protected $name;

    protected $socket;

    protected $success;

    protected $startTime;

    protected $maxRunTime = 300;

    public abstract function execute();

    public function setPid($pid) : Process
    {
        $this->pid = $pid;

        return $this;
    }

    public function getPid()
    {
        return $this->pid;
    }

    public function setSocket($socket) : Process
    {
        $this->socket = $socket;

        return $this;
    }

    public function getSocket()
    {
        return $this->socket;
    }

    public function setName(string $name) : Process
    {
        $this->name = $name;

        return $this;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function onSuccess(callable $callback)
    {
        $this->success = $callback;
    }

    public function getSuccess()
    {
        return $this->success;
    }

    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getStartTime()
    {
        return $this->startTime;
    }

    public function setMaxRunTime(int $maxRunTime) : Process
    {
        $this->maxRunTime = $maxRunTime;

        return $this;
    }

    public function getMaxRunTime() : int
    {
        return $this->maxRunTime;
    }

    public function triggerSuccess($output)
    {
        if (!$this->success) {
            return null;
        }

        return call_user_func_array($this->success, [$output]);
    }
}

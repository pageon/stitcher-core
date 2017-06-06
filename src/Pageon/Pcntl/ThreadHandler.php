<?php

namespace Pageon\Pcntl;

class ThreadHandler
{

    private $pid;

    private $socket;

    public function __construct($pid, $socket) {
        $this->pid = $pid;
        $this->socket = $socket;
    }

    /**
     * @return mixed
     */
    public function getPid() {
        return $this->pid;
    }

    /**
     * @return mixed
     */
    public function getSocket() {
        return $this->socket;
    }

}

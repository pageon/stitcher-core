<?php

namespace Brendt\Stitcher\Event;

use \Symfony\Component\EventDispatcher\Event as SymfonyEvent;

class Event extends SymfonyEvent
{
    private $data;
    private $eventHook;

    public function __construct($data = [], string $eventHook = null)
    {
        $this->data = $data;
        $this->eventHook = $eventHook;
    }

    public static function create($data = [], string $eventHook = null) : Event
    {
        return new self($data, $eventHook);
    }

    public function getData()
    {
        return $this->data;
    }

    public function getEventHook() : string
    {
        return $this->eventHook;
    }
}

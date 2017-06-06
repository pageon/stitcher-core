<?php

namespace Brendt\Stitcher\Event;

use \Symfony\Component\EventDispatcher\Event as SymfonyEvent;

class Event extends SymfonyEvent
{
    /**
     * @var array
     */
    private $data;

    /**
     * @var string
     */
    private $eventHook;

    /**
     * Event constructor.
     *
     * @param array       $data
     * @param string|null $eventHook
     */
    public function __construct($data = [], string $eventHook = null) {
        $this->data = $data;
        $this->eventHook = $eventHook;
    }

    /**
     * @param array       $data
     * @param string|null $eventHook
     *
     * @return Event
     */
    public static function create($data = [], string $eventHook = null) : Event {
        return new self($data, $eventHook);
    }

    /**
     * @return mixed
     */
    public function getData() {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getEventHook() : string {
        return $this->eventHook;
    }

}

<?php

namespace Brendt\Stitcher\Event;

use \Symfony\Component\EventDispatcher\Event as SymfonyEvent;

class Event extends SymfonyEvent
{
    private $data;

    /**
     * Event constructor.
     *
     * @param array $data
     */
    public function __construct($data = []) {
        $this->data = $data;
    }

    /**
     * @param array $data
     *
     * @return Event
     */
    public static function create($data = []) : Event {
        return new self($data);
    }

    public function getData() {
        return $this->data;
    }

}

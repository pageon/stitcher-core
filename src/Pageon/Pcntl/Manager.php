<?php

namespace Pageon\Pcntl;

use Brendt\Stitcher\Event\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Manager
{
    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher) {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function async(Process $process) {
        socket_create_pair(AF_UNIX, SOCK_STREAM, 0, $sockets);

        list($parentSocket, $childSocket) = $sockets;

        if (($pid = pcntl_fork()) == 0) {
            socket_close($childSocket);
            $output = serialize($process->execute());
            socket_write($parentSocket, $output);
            socket_close($parentSocket);

            exit;
        }

        socket_close($parentSocket);

        return new ThreadHandler($pid, $childSocket);
    }

    public function wait(ThreadHandlerCollection $threadHandlerCollection) {
        $output = [];

        /** @var ThreadHandler $threadHandler */
        foreach ($threadHandlerCollection as $threadHandler) {
            while (pcntl_waitpid($threadHandler->getPid(), $status) != -1) {
                $status = pcntl_wexitstatus($status);
            }

            $output = unserialize(socket_read($threadHandler->getSocket(), 4096));

            if ($output instanceof Event) {
                $this->eventDispatcher->dispatch($output->getEventHook(), $output);
            }

            socket_close($threadHandler->getSocket());
        }

        return $output;
    }
}

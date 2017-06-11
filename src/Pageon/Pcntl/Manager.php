<?php

namespace Pageon\Pcntl;

use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * This Manager is used to create and wait for forked processes which can be executed in parallel.
 * It's a wrapper around `ext-pcntl`.
 *
 * Class Manager
 * @package Pageon\Pcntl
 */
class Manager
{
    /**
     * Create an asynchronous process.
     *
     * @param Process $process The process to run asynchronous.
     *
     * @return Process The asynchronous process.
     */
    public function async(Process $process) : Process {
        socket_create_pair(AF_UNIX, SOCK_STREAM, 0, $sockets);

        list($parentSocket, $childSocket) = $sockets;

        if (($pid = pcntl_fork()) == 0) {
            socket_close($childSocket);
            $output = serialize($process->execute());
            socket_write($parentSocket, substr($output, 0, 4095));
            socket_close($parentSocket);

            exit;
        }

        socket_close($parentSocket);

        $process
            ->setStartTime(time())
            ->setPid($pid)
            ->setSocket($childSocket);

        return $process;
    }

    /**
     * Wait for a collection of processes to finish.
     *
     * @param ProcessCollection $processCollection
     *
     * @return array
     * @throws \Exception
     */
    public function wait(ProcessCollection $processCollection) {
        $output = [];
        $processes = $processCollection->toArray();

        do {
            usleep(100000);

            /** @var Process $process */
            foreach ($processes as $key => $process) {
                $processStatus = pcntl_waitpid($process->getPid(), $status, WNOHANG | WUNTRACED);

                switch ($processStatus) {
                    case $process->getPid():
                        $this->handleProcessSuccess($process);
                        unset($processes[$key]);

                        break;
                    case 0:
                        if ($process->getStartTime() + $process->getMaxRunTime() < time() || pcntl_wifstopped($status)) {
                            $this->handleProcessStop($process);

                            unset($processes[$key]);
                        }

                        break;
                    default:
                        throw new \Exception("Could not reliably manage {$process->getPid()}");
                }
            }
        } while (count($processes));

        return $output;
    }

    /**
     * Handle a successful process.
     *
     * @param Process $process
     */
    private function handleProcessSuccess(Process $process) {
        $output[] = unserialize(socket_read($process->getSocket(), 4096));
        socket_close($process->getSocket());

        $success = $process->getSuccess();
        if ($success) {
            call_user_func_array($success, [$process]);
        }
    }

    /**
     * Handle a stopped process.
     *
     * @param Process $process
     *
     * @throws \Exception
     */
    private function handleProcessStop(Process $process) {
        if (!posix_kill($process->getPid(), SIGKILL)) {
            throw new \Exception('Failed to kill ' . $process->getPid() . ': ' . posix_strerror(posix_get_last_error()));
        }
    }
}

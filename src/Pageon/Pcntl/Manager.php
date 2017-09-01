<?php

namespace Pageon\Pcntl;

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
    public function async(Process $process) : Process
    {
        $sockets = stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP);

        list($parentSocket, $childSocket) = $sockets;

        if (($pid = pcntl_fork()) == 0) {
            fclose($childSocket);
            $output = serialize($process->execute());
            fwrite($parentSocket, $output);
            fclose($parentSocket);

            exit;
        }

        fclose($parentSocket);

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
    public function wait(ProcessCollection $processCollection)
    {
        $output = [];
        $processes = $processCollection->toArray();

        do {
            usleep(100000);

            /** @var Process $process */
            foreach ($processes as $key => $process) {
                $processStatus = pcntl_waitpid($process->getPid(), $processStatus, WNOHANG | WUNTRACED);

                $this->handleProcessStatus($processStatus, $process, $processes, $key);
            }
        } while (count($processes));

        return $output;
    }

    /**
     * @param         $processStatus
     * @param Process $process
     * @param array   $processes
     * @param         $key
     *
     * @throws \Exception
     */
    private function handleProcessStatus($processStatus, Process $process, array &$processes, $key)
    {
        switch ($processStatus) {
            case $process->getPid():
                $this->handleProcessSuccess($process);
                unset($processes[$key]);

                break;
            case 0:
                if ($process->getStartTime() + $process->getMaxRunTime() < time() || pcntl_wifstopped($processStatus)) {
                    $this->handleProcessStop($process);

                    unset($processes[$key]);
                }

                break;
            default:
                throw new \Exception("Could not reliably manage {$process->getPid()}");
        }
    }

    /**
     * Handle a successful process.
     *
     * @param Process $process
     */
    private function handleProcessSuccess(Process $process)
    {
        $output = '';
        while (!feof($process->getSocket())) {
            $output .= fgets($process->getSocket());
        }

        fclose($process->getSocket());

        if ($success = $process->getSuccess()) {
            call_user_func_array($success, [unserialize($output), $process]);
        }
    }

    /**
     * Handle a stopped process.
     *
     * @param Process $process
     *
     * @throws \Exception
     */
    private function handleProcessStop(Process $process)
    {
        if (!posix_kill($process->getPid(), SIGKILL)) {
            throw new \Exception('Failed to kill ' . $process->getPid() . ': ' . posix_strerror(posix_get_last_error()));
        }
    }
}

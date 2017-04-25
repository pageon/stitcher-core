<?php

namespace Brendt\Test;

use Brendt\Stitcher\App;
use Brendt\Stitcher\Application\Console;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\StreamOutput;

abstract class CommandTestCase extends TestCase
{
    public function runCommand(string $command) : string {
        /** @var Console $application */
        $application = App::get('app.console');
        $application->setAutoExit(false);

        $fp = tmpfile();
        $input = new StringInput($command);
        $output = new StreamOutput($fp);

        $application->run($input, $output);

        fseek($fp, 0);
        $output = '';
        while (!feof($fp)) {
            $output = fread($fp, 4096);
        }
        fclose($fp);

        return $output;
    }
}

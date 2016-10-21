<?php

namespace brendt\stitcher\command;

use brendt\stitcher\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Question\Question;

class CleanCommand extends Command {

    const FORCE = 'force';

    protected function configure() {
        $this->setName('site:clean')
            ->setDescription('Do a cleanup')
            ->setHelp("This command cleans all generated files.")
            ->addOption(self::FORCE, InputArgument::OPTIONAL);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        Config::load();

        $force = $input->getOption(self::FORCE);
        $publicDir = Config::get('directories.public');
        $cacheDir = Config::get('directories.cache');

        if (!$force) {
            $questionHelper = $this->getHelper('question');
            $question = new Question("Are you sure you want to clean all generated files ({$publicDir} and {$cacheDir}) [y/N] ", false);

            if (!$questionHelper->ask($input, $output, $question)) {
                return;
            }
        }

        $fs = new Filesystem();
        $log = [];

        if ($fs->exists($publicDir)) {
            $fs->remove($publicDir);
            $log[] = "Removed the public directory: {$publicDir}";
        }

        if ($fs->exists($cacheDir)) {
            $fs->remove($cacheDir);

            $log[] = "Removed the cache directory: {$cacheDir}";
        }

        if (count($log)) {
            $output->writeln("Successfully cleaned up\n");

            foreach ($log as $line) {
                $output->writeln("- {$line}");
            }

            $output->writeln("\nRun <fg=green>site:install</> and <fg=green>site:generate</> to generate these files again.");
        } else {
            $output->writeln('No files were found');
        }


        return;

    }
}

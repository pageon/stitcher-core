<?php

namespace Brendt\Stitcher\Command;

use Brendt\Stitcher\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class CleanCommand extends Command
{

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
     * @return void
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
        $finder = new Finder();
        $log = [];

        if ($fs->exists($publicDir)) {
            $publicDirectories = $finder->directories()->in($publicDir);
            $directoryPaths = [];

            /** @var SplFileInfo $directory */
            foreach ($publicDirectories as $directory) {
                $directoryPaths[] = $directory->getPathname();
            }

            $fs->remove($directoryPaths);

            $publicFiles = $finder->files()->in($publicDir)->notName('.htaccess')->ignoreDotFiles(true);

            /** @var SplFileInfo $file */
            foreach ($publicFiles as $file) {
                $fs->remove($file->getPathname());
            }

            $log[] = "Cleaned the public directory: {$publicDir}";
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

            $output->writeln("\nRun <fg=green>site:generate</> to generate these files again.");
        } else {
            $output->writeln('No files were found');
        }
    }
}

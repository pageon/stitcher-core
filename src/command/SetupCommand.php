<?php

namespace brendt\stitcher\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Finder\Finder;

class SetupCommand extends Command {

    const DIRECTORY = 'dir';

    protected function configure() {
        $this->setName('site:setup')
            ->setDescription('Setup the src/ folder of your new website')
            ->setHelp("This command generated a new src/ folder with a basic setup.")
            ->addOption(self::DIRECTORY, null, InputOption::VALUE_REQUIRED, 'Set the root directory in which to setup the src/ folder', './');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $root = $input->getOption(self::DIRECTORY);

        if (!strpos('./', $root)) {
            $root = "./{$root}";
        }

        $fs = new Filesystem();
        $finder = new Finder();

        if ($fs->exists("{$root}/src")) {
            $questionHelper = $this->getHelper('question');
            $question = new Question('The src/ directory already exists, are you sure you want to continue? (Some files might be overwritten) [y/N] ', false);

            $overwrite = $questionHelper->ask($input, $output, $question);

            if (!$overwrite) {
                $output->writeln('Cancelling the setup, run <fg=green>stitcher site:setup</> again if you want to setup the site anyways.');
            }
        }

        $setupPath = __DIR__ . '/../../setup';
        $files = $finder->files()->in($setupPath);

        $fs->mkdir("{$root}/src");
        foreach ($files as $file) {
            if (!$fs->exists("{$root}/{$file->getRelativePath()}")) {
                $fs->mkdir("{$root}/src/{$file->getRelativePath()}");
            }

            $path = $file->getRelativePathname();
            if (!$fs->exists("{$root}/src/{$path}")) {
                $fs->touch("{$root}/src/{$path}");
            }

            $fs->dumpFile("{$root}/src/{$path}", $file->getContents());
        }

        if (!$fs->exists("{$root}/stitcher")) {
            $fs->copy(__DIR__ . '/../../stitcher', './stitcher');
        }

        $output->writeln("Example setup copied to {$root}.");
        $output->writeln('Run <fg=green>stitcher site:generate</> to generate the site.');

        return;
    }

}

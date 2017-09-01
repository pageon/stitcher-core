<?php

namespace Brendt\Stitcher\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class InstallCommand extends Command
{
    const DIRECTORY = 'dir';

    protected $fs;

    public function __construct()
    {
        parent::__construct(null);

        $this->fs = new Filesystem();
    }

    protected function configure()
    {
        $this->setName('install')
            ->setAliases(['site:install'])
            ->setDescription('Setup a new Stitcher installation')
            ->setHelp("This command will generate several files as a base installation. (Only if they don't exist yet.)
            
    - The src/ directory with some examples
    - The public/ directory
    - The dev/ directory
    - The stitcher console
    - A sample config.yml
    - Dev setup
");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $log = [];

        $srcDir = './src';
        $publicDir = './public';
        $devDir = './dev';
        $stitcherPath = './stitcher';
        $configPath = './config.yml';

        $installDir = __DIR__ . '/../../install';
        $installSrcDir = "{$installDir}/src";
        $installDevDir = "{$installDir}/dev";
        $installPublicDir = "{$installDir}/public";
        $installStitcherPath = "{$installDir}/stitcher";
        $installConfigPath = "{$installDir}/config.yml";

        if (!$this->fs->exists($configPath)) {
            $this->fs->copy($installConfigPath, $configPath);

            $log[] = "Copied a sample config.yml to {$configPath}";
        }

        if (!$this->fs->exists($stitcherPath)) {
            $this->fs->copy($installStitcherPath, $stitcherPath);

            $log[] = "Copied the Stitcher Console to {$stitcherPath}";
        }

        if (!$this->fs->exists($srcDir)) {
            $this->copyFolder($installSrcDir, $srcDir);

            $log[] = "Copied a sample src/ to {$srcDir}";
        }

        if (!$this->fs->exists($devDir)) {
            $this->copyFolder($installDevDir, $devDir);

            $log[] = "Copied a sample dev/ to {$devDir}";
        }

        if (!$this->fs->exists($publicDir)) {
            $this->copyFolder($installPublicDir, $publicDir);

            $log[] = "Copied the public/ dir to {$publicDir}";
        }

        if (!empty($log)) {
            $output->writeln("Stitcher was successfully installed!\n");
            foreach ($log as $line) {
                $output->writeln("- {$line}");
            }
        } else {
            $output->writeln("Stitcher has already been installed.");
        }
    }

    protected function copyFolder($src, $dst)
    {
        $finder = new Finder();
        /** @var SplFileInfo[] $srcFiles */
        $srcFiles = $finder->files()->in($src)->ignoreDotFiles(false);

        if (!$this->fs->exists($dst)) {
            $this->fs->mkdir($dst);
        }

        foreach ($srcFiles as $srcFile) {
            if (!$this->fs->exists("{$dst}/{$srcFile->getRelativePath()}")) {
                $this->fs->mkdir("{$dst}/{$srcFile->getRelativePath()}");
            }

            $path = $srcFile->getRelativePathname();
            if (!$this->fs->exists("{$dst}/{$path}")) {
                $this->fs->touch("{$dst}/{$path}");
            }

            $this->fs->dumpFile("{$dst}/{$path}", $srcFile->getContents());
        }
    }

    protected function checkContinue(InputInterface $input, OutputInterface $output)
    {
        $srcDir = App::getParameter('directory.src');

        if ($this->fs->exists($srcDir)) {
            $questionHelper = $this->getHelper('question');
            $question = new Question('The src/ directory already exists, are you sure you want to continue? (Some files might be overwritten) [y/N] ', false);

            $overwrite = $questionHelper->ask($input, $output, $question);

            if (!$overwrite) {
                $output->writeln('Cancelling the install, run <fg=green>stitcher site:install</> again if you want to install the site anyways.');

                return false;
            }
        }

        return true;
    }
}

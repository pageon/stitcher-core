<?php

namespace brendt\stitcher\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Finder\Finder;

class InstallCommand extends Command {

    const DIRECTORY = 'dir';

    protected $fs;

    /**
     * InstallCommand constructor.
     *
     * @param string $name
     */
    public function __construct($name = null) {
        parent::__construct($name);

        $this->fs = new Filesystem();
    }

    protected function configure() {
        $this->setName('site:install')
            ->setDescription('Setup a new Stitcher installation')
            ->setHelp("This command will generate several files as a base installation. (Only if they don't exist yet.)
            
    - The src/ directory with some examples
    - The public/ directory
    - The stitcher console
    - A sample config.yml
    - Dev setup
");
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $log = [];

        $srcDir = './src';
        $publicDir = './public';
        $stitcherPath = './stitcher';
        $configPath = './config.yml';
        $devDir = ".";
        $configDevPath = "{$devDir}/config.dev.yml";
        $htaccessDevPath= "{$devDir}/.htaccess";
        $indexDevPath= "{$devDir}/index.dev.php";

        $installDir = __DIR__ . '/../../install';
        $installSrcDir = "{$installDir}/src";
        $installPublicDir = "{$installDir}/public";
        $installStitcherPath = "{$installDir}/stitcher";
        $installConfigPath = "{$installDir}/config.yml";
        $installConfigDevPath = "{$installDir}/config.dev.yml";
        $installHtaccessDevPath= "{$installDir}/.htaccess";
        $installIndexDevPath= "{$installDir}/index.dev.php";

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

        if (!$this->fs->exists($publicDir)) {
            $this->copyFolder($installPublicDir, $publicDir);

            $log[] = "Copied the public/ dir to {$publicDir}";
        }

        if (!$this->fs->exists($configDevPath)) {
            $this->fs->copy($installConfigDevPath, $configDevPath);

            $log[] = "Copied a sample config.dev.yml to {$configDevPath}";
        }

        if (!$this->fs->exists($htaccessDevPath)) {
            $this->fs->copy($installHtaccessDevPath, $htaccessDevPath);

            $log[] = "Copied the development .htaccess to {$htaccessDevPath}";
        }

        if (!$this->fs->exists($indexDevPath)) {
            $this->fs->copy($installIndexDevPath, $indexDevPath);

            $log[] = "Copied the development index to {$indexDevPath}";
        }

        if (count($log)) {
            $output->writeln("Stitcher was successfully installed!\n");
            foreach ($log as $line) {
                $output->writeln("- {$line}");
            }
        } else {
            $output->writeln("Stitcher has already been installed.");
        }

        return;
    }

    protected function copyFolder($src, $dst) {
        $finder = new Finder();
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

    protected function checkContinue(InputInterface $input, OutputInterface $output) {
        $srcDir = Config::get('directory.src');

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

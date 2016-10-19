<?php

namespace brendt\stitcher\engine\smarty;

use \Smarty;
use Symfony\Component\Finder\Finder;
use brendt\stitcher\Config;

class SmartyEngine extends Smarty {

    public function __construct() {
        parent::__construct();

        $finder = new Finder();
        $templateFolders = $finder->directories()->in(Config::get('directories.src'))->name('template');

        foreach ($templateFolders as $templateDir) {
            $this->addTemplateDir($templateDir);
        }

        $this->setCompileDir(Config::get('directories.cache'));
        $this->addPluginsDir(__DIR__);

        $this->caching = false;
    }
}

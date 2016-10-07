<?php

namespace brendt\stitcher;

use Smarty;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Yaml;

class Stitcher {

    /**
     * @var array
     */
    protected $site;

    /**
     * @var SplFileInfo[]
     */
    protected $templates;

    /**
     * @var string
     */
    private $root;

    /**
     * Stitcher constructor.
     *
     * @param string $root
     * @param string $compileDir
     */
    public function __construct($root = './src', $compileDir = './.cache') {
        $this->root = $root;

        $this->smarty = new Smarty();
        $this->smarty->setTemplateDir("{$root}/template");
        $this->smarty->setCompileDir($compileDir);
        $this->smarty->caching = false;

        $this->loadSite();
        $this->loadTemplates();
    }

    public function stitch() {
        $blanket = [];

        return $blanket;
    }

    /**
     * @return $this
     */
    public function loadSite() {
        $finder = new Finder();
        $files = $finder->files()->in("{$this->root}/site")->name('*.yml');
        $site = [];

        foreach ($files as $file) {
            $site += Yaml::parse($file->getContents());
        }

        $this->site = $site;

        return $this;
    }

    /**
     * @return $this
     */
    public function loadTemplates() {
        $finder = new Finder();
        $files = $finder->files()->in("{$this->root}/template")->name('*.tpl');
        $templates = [];

        foreach ($files as $file) {
            $id = str_replace('.tpl', '', $file->getPathname());
            $templates[$id] = $file;
        }

        $this->templates = $templates;

        return $this;
    }

}



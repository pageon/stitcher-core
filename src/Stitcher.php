<?php

namespace brendt\stitcher;

use Smarty;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Yaml;

class Stitcher {

    /**
     * @var SplFileInfo[]
     */
    protected $templates;

    /**
     * @var string
     */
    private $root;

    /**
     * @var string
     */
    private $compileDir;

    /**
     * Stitcher constructor.
     *
     * @param string $root
     * @param string $compileDir
     */
    public function __construct($root = './src', $compileDir = './.cache') {
        $this->root = $root;
        $this->compileDir = $compileDir;
    }

    /**
     * @return Smarty
     */
    protected function getSmarty() {
        $smarty = new Smarty();
        $finder = new Finder();
        $templateFolders = $finder->directories()->in("{$this->root}")->name('template');

        foreach ($templateFolders as $templateDir) {
            $smarty->addTemplateDir($templateDir);
        }

        $smarty->setCompileDir($this->compileDir);
        $smarty->caching = false;

        return $smarty;
    }

    /**
     * @return array
     * @throws \SmartyException
     */
    public function stitch() {
        $blanket = [];
        $smarty = $this->getSmarty();
        $site = $this->loadSite();
        $templates = $this->loadTemplates();

        foreach ($site as $route => $page) {
            if (!isset($templates[$page['template']])) {
                continue;
            }

            $data = [];

            foreach ($data as $name => $value) {
                $smarty->assign($name, $value);
            }

            try {
                $template = $templates[$page['template']];
                $html = $smarty->fetch($template->getRealPath());
                $blanket[$route] = $html;
            } catch (\SmartyException $e) {
                throw $e;
            }

            $smarty->clearAllAssign();
        }

        return $blanket;
    }

    /**
     * @return array
     */
    public function loadSite() {
        $finder = new Finder();
        $files = $finder->files()->in("{$this->root}/site")->name('*.yml');
        $site = [];

        foreach ($files as $file) {
            $site += Yaml::parse($file->getContents());
        }

        return $site;
    }

    /**
     * @return SplFileInfo[]
     */
    public function loadTemplates() {
        $finder = new Finder();
        $files = $finder->files()->in("{$this->root}/template")->name('*.tpl');
        $templates = [];

        foreach ($files as $file) {
            $id = str_replace('.tpl', '', $file->getRelativePathname());
            $templates[$id] = $file;
        }

        return $templates;
    }

}



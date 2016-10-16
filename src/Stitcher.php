<?php

namespace brendt\stitcher;

use brendt\stitcher\factory\ProviderFactory;
use Smarty;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Yaml;

class Stitcher {

    /**
     * @var SplFileInfo[]
     */
    protected $templates;

    /**
     * @var ProviderFactory
     */
    protected $factory;

    /**
     * @var string
     */
    private $root;

    /**
     * @var string
     */
    private $compileDir;

    /**
     * @var string
     */
    private $publicDir;

    /**
     * @var ProviderFactory
     */
    private $providerFactory;

    /**
     * Stitcher constructor.
     */
    public function __construct() {
        $this->root = Config::get('directories.src');
        $this->publicDir = Config::get('directories.public');
        $this->compileDir = Config::get('directories.cache');

        $this->providerFactory = Config::getDependency('factory.provider');
    }

    /**
     * @return Smarty
     */
    protected function getSmarty() {
        $smarty = new Smarty();
        $finder = new Finder();
        $templateFolders = $finder->directories()->in($this->root)->name('template');

        foreach ($templateFolders as $templateDir) {
            $smarty->addTemplateDir($templateDir);
        }

        $smarty->setCompileDir($this->compileDir);
        $smarty->caching = false;

        return $smarty;
    }

    public function save($blanket) {
        $fs = new Filesystem();

        $publicDirExists = $fs->exists($this->publicDir);
        if (!$publicDirExists) {
            $fs->mkdir($this->publicDir);
        }

        $htaccessExists = $fs->exists($this->publicDir . '/.htaccess');
        if (!$htaccessExists) {
            $fs->copy(__DIR__ . '/.htaccess', $this->publicDir . '/.htaccess');
        }

        foreach ($blanket as $path => $page) {
            if ($path === '/') {
                $path = 'index';
            }

            $fs->dumpFile($this->publicDir . "/{$path}.html", $page);
        }
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
     * @param string|array $routes
     *
     * @return array
     * @throws \SmartyException
     */
    public function stitch($routes = []) {
        $blanket = [];
        $smarty = $this->getSmarty();
        $site = $this->loadSite();
        $templates = $this->loadTemplates();

        if (is_string($routes)) {
            $routes = [$routes];
        }

        foreach ($site as $route => $page) {
            $skipRoute = count($routes) && !in_array($route, $routes);
            $templateIsset = isset($templates[$page['template']]);

            if ($skipRoute || !$templateIsset) {
                continue;
            }

            $template = $templates[$page['template']];
            $detailVariable = null;
            $globalVariables = [];

            foreach ($page['data'] as $name => $variable) {
                if (is_array($variable) && isset($variable['src']) && isset($variable['id'])) {
                    $detailVariable = [
                        'name' => $name,
                        'src' => $variable['src'],
                        'id' => $variable['id'],
                    ];
                } else if (is_string($variable)) {
                    $globalVariables[$name] = $this->getData($variable);
                }
            }

            foreach ($globalVariables as $name => $variable) {
                $smarty->assign($name, $variable);
            }

            if ($detailVariable) {
                $idField = $detailVariable['id'];
                $entries = $this->getData($detailVariable['src']);
                $entryName = $detailVariable['name'];

                foreach ($entries as $entry) {
                    if (!isset($entry[$idField])) {
                        continue;
                    }

                    $routeName = str_replace('{' . $idField . '}', $entry[$idField], $route);

                    $smarty->assign($entryName, $entry);
                    $blanket[$routeName] = $smarty->fetch($template->getRealPath());
                    $smarty->clearAssign($entryName);
                }
            } else {
                $blanket[$route] = $smarty->fetch($template->getRealPath());
            }
        }

        return $blanket;
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

    private function getData($src) {
        $provider = $this->providerFactory->getProvider($src);

        if (!$provider) {
            return $src;
        }

        return $provider->parse($src);
    }

}



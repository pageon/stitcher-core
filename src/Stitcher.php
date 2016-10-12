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

    // TODO: Implement config
    /**
     * @var string
     */
    public static $publicDir = './public';

    // TODO: Implement Service Container
    /**
     * @var ProviderFactory|null
     */
    public static $providerFactory = null;

    /**
     * Stitcher constructor.
     *
     * @param string $root
     * @param string $publicDir
     * @param string $compileDir
     */
    public function __construct($root = './src', $publicDir = './public', $compileDir = './.cache') {
        $this->root = $root;
        $this->compileDir = $compileDir;
        self::$publicDir = $publicDir;
        self::$providerFactory = new ProviderFactory("{$this->root}/data", $publicDir);
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

    public function save($blanket) {
        $fs = new Filesystem();

        $publicDirExists = $fs->exists(self::$publicDir);
        if (!$publicDirExists) {
            $fs->mkdir(self::$publicDir);
        }

        $htaccessExists = $fs->exists(self::$publicDir . '/.htaccess');
        if (!$htaccessExists) {
            $fs->copy(__DIR__ . '/.htaccess', self::$publicDir . '.htaccess');
        }

        foreach ($blanket as $path => $page) {
            if ($path === '/') {
                $path = 'index';
            }

            $fs->dumpFile(self::$publicDir . "/{$path}.html", $page);
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

            $data = $this->getDataForPage($page);

            $routeVariables = [];
            preg_match('/{[\w]+}/', $route, $routeVariables);
            $routeVariables = array_map(function($variable) {
                return trim(trim($variable, '{'), '}');
            }, $routeVariables);

            if (count($routeVariables)) {
                $routeVariable = reset($routeVariables);

                foreach ($data as $name => $entries) {
                    foreach ($entries as $entry) {
                        if (!isset($entry[$routeVariable])) {
                            continue;
                        }

                        $var = $entry[$routeVariable];
                        $routeName = str_replace('{' .$routeVariable. '}', $var, $route);
                        $smarty->assign($name, $entry);

                        try {
                            $template = $templates[$page['template']];
                            $html = $smarty->fetch($template->getRealPath());
                            $blanket[$routeName] = $html;
                        } catch (\SmartyException $e) {
                            throw $e;
                        }
                    }
                }
            } else {
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
            }

            $smarty->clearAllAssign();
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

    /**
     * @param $page
     *
     * @return array
     */
    private function getDataForPage($page) {
        $data = [];

        if (!isset($page['data'])) {
            return $data;
        }

        foreach ($page['data'] as $name => $entry) {
            $provider = self::$providerFactory->getProvider($entry);

            if (!$provider) {
                continue;
            }

            $data[$name] = $provider->parse($entry);
        }

        return $data;
    }

}



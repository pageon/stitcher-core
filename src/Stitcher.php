<?php

namespace brendt\stitcher;

use brendt\stitcher\provider\Provider;
use brendt\stitcher\site\Page;
use brendt\stitcher\exception\InvalidSiteException;
use brendt\stitcher\exception\TemplateNotFoundException;
use brendt\stitcher\factory\AdapterFactory;
use brendt\stitcher\factory\ProviderFactory;
use brendt\stitcher\factory\TemplateEngineFactory;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use brendt\stitcher\engine\TemplateEngine;
use brendt\stitcher\site\Site;

/**
 * The Stitcher class is the core compiler of every Stitcher application. This class takes care of all routes, pages, templates and data,
 * and "stitches" everything together.
 *
 * The stitching process is done in several steps, with the final result being a fully rendered website in the `directories.public` folder.
 */
class Stitcher {

    /**
     * @var SplFileInfo[]
     */
    protected $templates;

    /**
     * @var TemplateEngine
     */
    private $templateEngine;

    /**
     * Stitcher constructor.
     */
    public function __construct() {
        /** @var TemplateEngineFactory $templateEngineFactory */
        $templateEngineFactory = Config::getDependency('factory.template.engine');
        $this->templateEngine = $templateEngineFactory->getByType(Config::get('engines.template'));
    }

    /**
     * @return Site
     * @throws InvalidSiteException
     */
    public function loadSite() {
        $site = new Site();
        $finder = new Finder();
        $src = Config::get('directories.src');
        $files = $finder->files()->in("{$src}/site")->name('*.yml');

        foreach ($files as $file) {
            try {
                $fileContents = Yaml::parse($file->getContents());
            } catch (ParseException $e) {
                throw new InvalidSiteException("{$file->getRelativePathname()}: {$e->getMessage()}");
            }

            if (!is_array($fileContents)) {
                continue;
            }

            foreach ($fileContents as $route => $data) {
                $page = new Page($route, $data);

                $site->addPage($page);
            }
        }

        return $site;
    }

    /**
     * @return SplFileInfo[]
     */
    public function loadTemplates() {
        $finder = new Finder();
        $templateExtension = $this->templateEngine->getTemplateExtension();
        $templateFolder = Config::get('directories.template') ? Config::get('directories.template') : Config::get('directories.src') . '/template';
        $files = $finder->files()->in($templateFolder)->name("*.{$templateExtension}");
        $templates = [];

        foreach ($files as $file) {
            $id = str_replace(".{$templateExtension}", '', $file->getRelativePathname());
            $templates[$id] = $file;
        }

        return $templates;
    }

    /**
     * @param string|array $routes
     * @param null         $filterValue
     *
     * @return array
     * @throws TemplateNotFoundException
     */
    public function stitch($routes = [], $filterValue = null) {
        $blanket = [];

        $site = $this->loadSite();
        $templates = $this->loadTemplates();

        if (is_string($routes)) {
            $routes = [$routes];
        }

        foreach ($site as $page) {
            $route = $page->getId();

            $skipRoute = count($routes) && !in_array($route, $routes);
            if ($skipRoute) {
                continue;
            }

            $templateIsset = isset($templates[$page->getTemplatePath()]);

            if (!$templateIsset) {
                if (isset($page['template'])) {
                    throw new TemplateNotFoundException("Template {$page['template']} not found.");
                } else {
                    throw new TemplateNotFoundException('No template was set.');
                }
            }

            $pages = $this->parseAdapters($page, $filterValue);

            $pageTemplate = $templates[$page->getTemplatePath()];
            foreach ($pages as $entryPage) {
                $entryPage = $this->parseVariables($entryPage);

                // Render each page
                $this->templateEngine->addTemplateVariables($entryPage->getVariables());
                $blanket[$entryPage->getId()] = $this->templateEngine->renderTemplate($pageTemplate);
                $this->templateEngine->clearTemplateVariables();
            }
        }

        return $blanket;
    }

    /**
     * @param Page $page
     * @param null $entryId
     *
     * @return Page[]
     */
    public function parseAdapters(Page $page, $entryId = null) {
        /** @var AdapterFactory $adapterFactory */
        $adapterFactory = Config::getDependency('factory.adapter');
        $pages = [];

        // TODO: this will bug with multiple adapters
        if (count($page->getAdapters())) {
            foreach ($page->getAdapters() as $type => $adapterConfig) {
                $adapter = $adapterFactory->getByType($type);

                if ($entryId) {
                    $pages = $adapter->transform($page, $entryId);
                } else {
                    $pages = $adapter->transform($page);
                }
            }
        } else {
            $pages = [$page->getId() => $page];
        }

        return $pages;
    }

    /**
     * @param Page $page
     *
     * @return Page
     */
    public function parseVariables(Page $page) {
        foreach ($page->getVariables() as $name => $value) {
            if ($page->isParsedVariable($name)) {
                continue;
            }

            $page
                ->setVariableValue($name, $this->getData($value))
                ->setVariableIsParsed($name);
        }

        return $page;
    }

    /**
     * @param array $blanket
     */
    public function save(array $blanket) {
        $fs = new Filesystem();
        $public = Config::get('directories.public');

        if (!$fs->exists($public)) {
            $fs->mkdir($public);
        }

        foreach ($blanket as $path => $page) {
            if ($path === '/') {
                $path = 'index';
            }

            $fs->dumpFile($public . "/{$path}.html", $page);
        }
    }

    private function getData($src) {
        /** @var ProviderFactory $providerFactory */
        $providerFactory = Config::getDependency('factory.provider');
        $provider = $providerFactory->getProvider($src);

        if (!$provider) {
            return $src;
        }

        return $provider->parse($src);
    }

}



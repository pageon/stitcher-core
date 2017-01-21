<?php

namespace brendt\stitcher;

use brendt\stitcher\exception\InvalidSiteException;
use brendt\stitcher\exception\TemplateNotFoundException;
use brendt\stitcher\factory\AdapterFactory;
use brendt\stitcher\factory\ParserFactory;
use brendt\stitcher\factory\TemplateEngineFactory;
use brendt\stitcher\site\Page;
use brendt\stitcher\site\Site;
use brendt\stitcher\template\TemplateEngine;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * The Stitcher class is the core compiler of every Stitcher application. This class takes care of all routes, pages,
 * templates and data, and "stitches" everything together.
 *
 * The stitching process is done in several steps, with the final result being a fully rendered website in the
 * `directories.public` folder.
 */
class Stitcher {

    /**
     * A collection of all templates available when rendering a Stitcher application.
     *
     * @var SplFileInfo[]
     */
    protected $templates;

    /**
     * The template engine which is configured via `engines.template`.
     *
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
     * The core stitcher function. This function will compile the configured site and return an array of parsed
     * data.
     *
     * Compiling a site is done in the following steps.
     *
     *      - Load the site configuration @see \brendt\stitcher\Stitcher::loadSite()
     *      - Load all available templates @see \brendt\stitcher\Stitcher::loadTemplates()
     *      - Loop over all pages and transform every page with the configured adapters (in any are set) @see
     *      \brendt\stitcher\Stitcher::parseAdapters()
     *      - Loop over all transformed pages and parse the variables which weren't parsed by the page's adapters. @see
     *      \brendt\stitcher\Stitcher::parseVariables()
     *      - Add all variables to the template engine and render the HTML for each page.
     *
     * This function takes two optional parameters which are used to render pages on the fly when using the
     * developer controller. The first one, `routes` will take a string or array of routes which should be rendered,
     * instead of all available routes. The second one, `filterValue` is used to provide a filter when the
     * CollectionAdapter is used, and only one entry page should be rendered.
     *
     * @param string|array $routes
     * @param string       $filterValue
     *
     * @return array
     * @throws TemplateNotFoundException
     *
     * @see \brendt\stitcher\Stitcher::save()
     * @see \brendt\stitcher\controller\DevController::run()
     * @see \brendt\stitcher\adapter\CollectionAdapter::transform()
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
     * Load a site from YAML configuration files in the `directories.src`/site directory.
     * All YAML files are loaded and parsed into Page objects and added to a Site collection.
     *
     * @return Site
     * @throws InvalidSiteException
     *
     * @see \brendt\stitcher\site\Page
     * @see \brendt\stitcher\site\Site
     */
    public function loadSite() {
        $src = Config::get('directories.src');
        $files = Finder::create()->files()->in("{$src}/site")->name('*.yml');
        $site = new Site();

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
     * Load all templates from either the `directories.template` directory. Depending on the configured template
     * engine, set with `engines.template`; .html or .tpl files will be loaded.
     *
     * @return SplFileInfo[]
     */
    public function loadTemplates() {
        $templateFolder = Config::get('directories.template');
        $templateExtension = $this->templateEngine->getTemplateExtension();
        $files = Finder::create()->files()->in($templateFolder)->name("*.{$templateExtension}");
        $templates = [];

        foreach ($files as $file) {
            $id = str_replace(".{$templateExtension}", '', $file->getRelativePathname());
            $templates[$id] = $file;
        }

        return $templates;
    }

    /**
     * This function takes a page and optional entry id. The page's adapters will be loaded and looped.
     * An adapter will transform a page's original configuration and variables to one or more pages.
     * An entry id can be provided as a filter. This filter can be used in an adapter to skip rendering unnecessary
     * pages. The filter parameter is used to render pages on the fly when using the developer controller.
     *
     * @param Page   $page
     * @param string $entryId
     *
     * @return Page[]
     *
     * @see  \brendt\stitcher\adapter\Adapter::transform()
     * @see  \brendt\stitcher\controller\DevController::run()
     *
     * @todo When a page has multiple adapters, this function won't correctly parse more than one. This is considered a
     *       bug, but not a major one because there are only two adapters at this moment, and they can not be used
     *       together anyway.
     *
     */
    public function parseAdapters(Page $page, $entryId = null) {
        /** @var AdapterFactory $adapterFactory */
        $adapterFactory = Config::getDependency('factory.adapter');
        $pages = [];

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
     * This function takes a Page object and parse its variables using a Parser. It will only parse variables which
     * weren't parsed already by an adapter.
     *
     * @param Page $page
     *
     * @return Page
     *
     * @see \brendt\stitcher\factory\ParserFactory
     * @see \brendt\stitcher\parser\Parser
     * @see \brendt\stitcher\site\Page::isParsedVariable()
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
     * This function will save a stitched output to HTML files in the `directories.public` directory.
     *
     * @param array $blanket
     *
     * @see \brendt\stitcher\Stitcher::stitch()
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

    /**
     * This function will get the parser based on the value. This value is parsed by the parser, or returned if no
     * suitable parser was found.
     *
     * @param $value
     *
     * @return mixed
     *
     * @see \brendt\stitcher\factory\ParserFactory
     */
    private function getData($value) {
        /** @var ParserFactory $parserFactory */
        $parserFactory = Config::getDependency('factory.parser');
        $parser = $parserFactory->getParser($value);

        if (!$parser) {
            return $value;
        }

        return $parser->parse($value);
    }

}



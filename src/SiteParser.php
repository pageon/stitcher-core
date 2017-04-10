<?php

namespace Brendt\Stitcher;

use Brendt\Stitcher\Adapter\Adapter;
use Brendt\Stitcher\Factory\AdapterFactory;
use Brendt\Stitcher\Factory\ParserFactory;
use Brendt\Stitcher\Factory\TemplateEngineFactory;
use Brendt\Stitcher\Site\Page;
use Brendt\Stitcher\Site\Site;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Yaml;

class SiteParser
{
    /**
     * @var string
     */
    private $filter;

    /**
     * @var string
     */
    private $srcDir;

    /**
     * @var string
     */
    private $templateDir;

    /**
     * @var ParserFactory
     */
    private $parserFactory;

    /**
     * @var TemplateEngineFactory
     */
    private $templateEngineFactory;

    /**
     * @var AdapterFactory
     */
    private $adapterFactory;

    /**
     * SiteParser constructor.
     *
     * @param string                $srcDir
     * @param string                $templateDir
     * @param ParserFactory         $parserFactory
     * @param TemplateEngineFactory $templateEngineFactory
     * @param AdapterFactory        $adapterFactory
     */
    public function __construct(string $srcDir, string $templateDir, ParserFactory $parserFactory, TemplateEngineFactory $templateEngineFactory, AdapterFactory $adapterFactory) {
        $this->srcDir = $srcDir;
        $this->templateDir = $templateDir;
        $this->parserFactory = $parserFactory;
        $this->templateEngineFactory = $templateEngineFactory;
        $this->adapterFactory = $adapterFactory;
    }

    /**
     * Parse a path into usable data.
     *
     * @param array  $routes
     * @param string $filterValue
     *
     * @return mixed
     */
    public function parse($routes = [], string $filterValue = null) : array {
        $templateEngine = $this->templateEngineFactory->getDefault();
        $blanket = [];

        $site = $this->loadSite((array) $routes);
        $templates = $this->loadTemplates();

        foreach ($site as $page) {
            $templateIsset = isset($templates[$page->getTemplatePath()]);

            if (!$templateIsset) {
                if ($template = $page->getTemplatePath()) {
                    throw new TemplateNotFoundException("Template {$template} not found.");
                } else {
                    throw new TemplateNotFoundException('No template was set.');
                }
            }

            $pages = $this->parseAdapters($page, $filterValue);

            $pageTemplate = $templates[$page->getTemplatePath()];
            foreach ($pages as $entryPage) {
                $entryPage = $this->parseVariables($entryPage);

                // Render each page
                $templateEngine->addTemplateVariables($entryPage->getVariables());
                $blanket[$entryPage->getId()] = $templateEngine->renderTemplate($pageTemplate);
                $templateEngine->clearTemplateVariables();
            }
        }

        return $blanket;
    }

    /**
     * Load a site from YAML configuration files in the `directories.src`/site directory.
     * All YAML files are loaded and parsed into Page objects and added to a Site collection.
     *
     * @param array $routes
     *
     * @return Site
     * @throws InvalidSiteException
     * @see \Brendt\Stitcher\Site\Page
     * @see \Brendt\Stitcher\Site\Site
     */
    public function loadSite(array $routes = []) : Site {
        /** @var SplFileInfo[] $files */
        $files = Finder::create()->files()->in("{$this->srcDir}/site")->name('*.yml');
        $site = new Site();

        foreach ($files as $file) {
            try {
                $fileContents = (array) Yaml::parse($file->getContents());
            } catch (ParseException $e) {
                throw new InvalidSiteException("{$file->getRelativePathname()}: {$e->getMessage()}");
            }

            foreach ($fileContents as $route => $data) {
                if (count($routes) && !in_array($route, $routes)) {
                    continue;
                }

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
        $templateEngine = $this->templateEngineFactory->getDefault();
        $templateExtension = $templateEngine->getTemplateExtension();

        /** @var SplFileInfo[] $files */
        $files = Finder::create()->files()->in($this->templateDir)->name("*.{$templateExtension}");
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
     * @see  \Brendt\Stitcher\Adapter\Adapter::transform()
     * @see  \Brendt\Stitcher\Controller\DevController::run()
     */
    public function parseAdapters(Page $page, $entryId = null) {
        if (!$page->getAdapters()) {
            return [$page->getId() => $page];
        }

        $pages = [$page];

        foreach ($page->getAdapters() as $type => $adapterConfig) {
            $adapter = $this->adapterFactory->getByType($type);

            if ($entryId !== null) {
                $pages = $adapter->transform($pages, $entryId);
            } else {
                $pages = $adapter->transform($pages);
            }
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
     * @see \Brendt\Stitcher\Factory\ParserFactory
     * @see \Brendt\Stitcher\Parser\Parser
     * @see \Brendt\Stitcher\Site\Page::isParsedVariable()
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
     * @param string $filter
     *
     * @return SiteParser
     */
    public function setFilter(string $filter) : SiteParser {
        $this->filter = $filter;

        return $this;
    }

    /**
     * This function will get the parser based on the value. This value is parsed by the parser, or returned if no
     * suitable parser was found.
     *
     * @param $value
     *
     * @return mixed
     *
     * @see \Brendt\Stitcher\Factory\ParserFactory
     */
    private function getData($value) {
        $parser = $this->parserFactory->getByFileName($value);

        if (!$parser) {
            return $value;
        }

        return $parser->parse($value);
    }
}

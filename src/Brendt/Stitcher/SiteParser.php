<?php

namespace Brendt\Stitcher;

use Brendt\Html\Meta\Meta;
use Brendt\Stitcher\Exception\TemplateNotFoundException;
use Brendt\Stitcher\Factory\AdapterFactory;
use Brendt\Stitcher\Factory\HeaderCompilerFactory;
use Brendt\Stitcher\Factory\ParserFactory;
use Brendt\Stitcher\Factory\TemplateEngineFactory;
use Brendt\Stitcher\Site\Http\HeaderCompiler;
use Brendt\Stitcher\Site\Meta\MetaCompiler;
use Brendt\Stitcher\Site\Page;
use Brendt\Stitcher\Site\Site;
use Brendt\Stitcher\Template\TemplateEngine;
use Brendt\Stitcher\Template\TemplatePlugin;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Exception\ParseException;
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
     * @var HeaderCompilerFactory
     */
    private $headerCompilerFactory;

    /**
     * @var array
     */
    private $metaConfig;

    /**
     * @var TemplatePlugin
     */
    private $templatePlugin;

    /**
     * @var HeaderCompiler|null
     */
    private $headerCompiler;

    /**
     * @var TemplateEngine
     */
    private $templateEngine;

    /**
     * @var SplFileInfo[]
     */
    private $templates;

    /**
     * @var MetaCompiler
     */
    private $metaCompiler;

    /**
     * SiteParser constructor.
     *
     * @param string                $srcDir
     * @param string                $templateDir
     * @param TemplatePlugin        $templatePlugin
     * @param ParserFactory         $parserFactory
     * @param TemplateEngineFactory $templateEngineFactory
     * @param AdapterFactory        $adapterFactory
     * @param HeaderCompilerFactory $headerCompilerFactory
     * @param MetaCompiler          $metaCompiler
     * @param array                 $metaConfig
     */
    public function __construct(
        string $srcDir,
        string $templateDir,
        TemplatePlugin $templatePlugin,
        ParserFactory $parserFactory,
        TemplateEngineFactory $templateEngineFactory,
        AdapterFactory $adapterFactory,
        HeaderCompilerFactory $headerCompilerFactory,
        MetaCompiler $metaCompiler,
        array $metaConfig = []
    ) {
        $this->srcDir = $srcDir;
        $this->templateDir = $templateDir;
        $this->templatePlugin = $templatePlugin;
        $this->parserFactory = $parserFactory;
        $this->templateEngineFactory = $templateEngineFactory;
        $this->adapterFactory = $adapterFactory;
        $this->headerCompilerFactory = $headerCompilerFactory;
        $this->metaCompiler = $metaCompiler;
        $this->metaConfig = $metaConfig;

        $this->headerCompiler = $this->headerCompilerFactory->getHeaderCompilerByEnvironment();
        $this->templateEngine = $this->templateEngineFactory->getDefault();
        $this->templates = $this->loadTemplates();
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

                $page = new Page($route, $data, $this->createMeta());
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
        $templateExtension = $this->templateEngine->getTemplateExtension();

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
     * Parse a path into usable data.
     *
     * @param array  $routes
     * @param string $filterValue
     *
     * @return array|mixed
     * @throws TemplateNotFoundException
     */
    public function parse($routes = [], string $filterValue = null) : array {
        $blanket = [];

        $site = $this->loadSite((array) $routes);

        foreach ($site as $page) {
            $templateIsset = isset($this->templates[$page->getTemplatePath()]);

            if (!$templateIsset) {
                if ($template = $page->getTemplatePath()) {
                    throw new TemplateNotFoundException("Template {$template} not found.");
                } else {
                    throw new TemplateNotFoundException('No template was set.');
                }
            }

            $pages = $this->parseAdapters($page, $filterValue);

            foreach ($pages as $entryPage) {
                $blanket[$entryPage->getId()] = $this->parsePage($entryPage);
            }
        }

        return $blanket;
    }

    /**
     * @param Page $page
     *
     * @return string
     */
    public function parsePage(Page $page) : string {
        $entryPage = $this->parseVariables($page);
        $this->metaCompiler->compilePage($page);

        $this->templatePlugin->setPage($entryPage);
        $this->templateEngine->addTemplateVariables($entryPage->getVariables());

        $pageTemplate = $this->templates[$page->getTemplatePath()];
        $result = $this->templateEngine->renderTemplate($pageTemplate);
        
        if ($this->headerCompiler) {
            $this->headerCompiler->compilePage($page);
        }

        $this->templateEngine->clearTemplateVariables();

        return $result;
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
     * @see  \Brendt\Stitcher\Application\DevController::run()
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

    private function createMeta() : Meta {
        $meta = new Meta();

        return $meta;
    }
}

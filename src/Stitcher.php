<?php

namespace Brendt\Stitcher;

use AsyncInterop\Promise;
use Brendt\Stitcher\Adapter\Adapter;
use Brendt\Stitcher\Exception\InvalidSiteException;
use Brendt\Stitcher\Exception\TemplateNotFoundException;
use Brendt\Stitcher\Factory\ParserFactory;
use Brendt\Stitcher\Factory\TemplateEngineFactory;
use Brendt\Stitcher\Site\Page;
use Brendt\Stitcher\Site\Site;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
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
class Stitcher
{
    /**
     * @var ContainerBuilder
     */
    protected static $container;

    /**
     * @var array
     */
    protected static $configDefaults = [
        'directories.src'      => './src',
        'directories.public'   => './public',
        'directories.cache'    => './.cache',
        'meta'                 => [],
        'minify'               => false,
        'engines.template'     => 'smarty',
        'engines.image'        => 'gd',
        'engines.optimizer'    => true,
        'caches.image'         => true,
    ];

    /**
     * A collection of promises representing Stitcher's state.
     *
     * @var Promise[]
     */
    private $promises = [];

    /**
     * @var string
     */
    private $srcDir;

    /**
     * @var string
     */
    private $publicDir;

    /**
     * @var string
     */
    private $templateDir;

    /**
     * @see \Brendt\Stitcher\Stitcher::create()
     *
     * @param string $srcDir
     * @param string $publicDir
     * @param string $templateDir
     */
    private function __construct(?string $srcDir = './src', ?string $publicDir = './public', ?string $templateDir = './src/template') {
        $this->srcDir = $srcDir;
        $this->publicDir = $publicDir;
        $this->templateDir = $templateDir;
    }

    /**
     * Static constructor
     *
     * @param string $configPath
     * @param array  $defaultConfig
     *
     * @return Stitcher
     *
     */
    public static function create(string $configPath = './config.yml', array $defaultConfig = []) : Stitcher {
        self::$container = new ContainerBuilder();

        $configPathParts = explode('/', $configPath);
        $configFileName = array_pop($configPathParts);
        $configPath = implode('/', $configPathParts) . '/';
        $configFiles = Finder::create()->files()->in($configPath)->name($configFileName);
        $srcDir = null;
        $publicDir = null;
        $templateDir = null;

        /** @var SplFileInfo $configFile */
        foreach ($configFiles as $configFile) {
            $config = array_merge(
                self::$configDefaults,
                Yaml::parse($configFile->getContents()),
                $defaultConfig
            );

            $flatConfig = Config::flatten($config);
            $flatConfig['directories.template'] = $flatConfig['directories.template'] ?? $flatConfig['directories.src'];

            foreach ($flatConfig as $key => $value) {
                self::$container->setParameter($key, $value);
            }

            $srcDir = $flatConfig['directories.src'] ?? $srcDir;
            $publicDir = $flatConfig['directories.public'] ?? $publicDir;
            $templateDir = $flatConfig['directories.template'] ?? $templateDir;

            if (isset($config['meta'])) {
                self::$container->setParameter('meta', $config['meta']);
            }
        }

        $stitcher = new self($srcDir, $publicDir, $templateDir);
        self::$container->set('stitcher', $stitcher);

        $serviceLoader = new YamlFileLoader(self::$container, new FileLocator(__DIR__));
        $serviceLoader->load('services.yml');

        return $stitcher;
    }

    /**
     * @param string $id
     *
     * @return mixed
     */
    public static function get(string $id) {
        return self::$container->get($id);
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public static function getParameter(string $key) {
        return self::$container->getParameter($key);
    }

    /**
     * The core stitcher function. This function will compile the configured site and return an array of parsed
     * data.
     *
     * Compiling a site is done in the following steps.
     *
     *      - Load the site configuration @see \Brendt\Stitcher\Stitcher::loadSite()
     *      - Load all available templates @see \Brendt\Stitcher\Stitcher::loadTemplates()
     *      - Loop over all pages and transform every page with the configured adapters (in any are set) @see
     *      \Brendt\Stitcher\Stitcher::parseAdapters()
     *      - Loop over all transformed pages and parse the variables which weren't parsed by the page's adapters. @see
     *      \Brendt\Stitcher\Stitcher::parseVariables()
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
     * @see \Brendt\Stitcher\Stitcher::save()
     * @see \Brendt\Stitcher\Controller\DevController::run()
     * @see \Brendt\Stitcher\Adapter\CollectionAdapter::transform()
     */
    public function stitch($routes = [], string $filterValue = null) {
        /** @var TemplateEngineFactory $templateEngineFactory */
        $templateEngineFactory = self::get('factory.template');
        $templateEngine = $templateEngineFactory->getDefault();

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
     * @return Site
     * @throws InvalidSiteException
     *
     * @see \Brendt\Stitcher\Site\Page
     * @see \Brendt\Stitcher\Site\Site
     */
    public function loadSite() {
        /** @var SplFileInfo[] $files */
        $files = Finder::create()->files()->in("{$this->srcDir}/site")->name('*.yml');
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
        /** @var TemplateEngineFactory $templateEngineFactory */
        $templateEngineFactory = self::get('factory.template');
        $templateEngine = $templateEngineFactory->getDefault();
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
            /** @var Adapter $adapter */
            $adapter = self::get("adapter.{$type}");

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
     * This function will save a stitched output to HTML files in the `directories.public` directory.
     *
     * @param array $blanket
     *
     * @see \Brendt\Stitcher\Stitcher::stitch()
     */
    public function save(array $blanket) {
        $fs = new Filesystem();

        foreach ($blanket as $path => $page) {
            if ($path === '/') {
                $path = 'index';
            }
            
            $fs->dumpFile($this->publicDir . "/{$path}.html", $page);
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
     * @see \Brendt\Stitcher\Factory\ParserFactory
     */
    private function getData($value) {
        /** @var ParserFactory $parserFactory */
        $parserFactory = self::get('factory.parser');
        $parser = $parserFactory->getByFileName($value);

        if (!$parser) {
            return $value;
        }

        return $parser->parse($value);
    }

    /**
     * @param Promise $promise
     *
     * @return Stitcher
     */
    public function addPromise(?Promise $promise) : Stitcher {
        if ($promise) {
            $this->promises[] = $promise;
        }

        return $this;
    }

    /**
     * @param callable $callback
     */
    public function done(callable $callback) {
        $donePromise = \Amp\all($this->promises);

        $donePromise->when($callback);
    }

}



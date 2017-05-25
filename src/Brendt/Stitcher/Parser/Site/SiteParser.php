<?php

namespace Brendt\Stitcher\Parser\Site;

use Pageon\Html\Meta\Meta;
use Brendt\Stitcher\Event\Event;
use Brendt\Stitcher\Exception\InvalidSiteException;
use Brendt\Stitcher\Exception\TemplateNotFoundException;
use Brendt\Stitcher\Site\Http\Htaccess;
use Brendt\Stitcher\Site\Page;
use Brendt\Stitcher\Site\Site;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class SiteParser
{
    const EVENT_PARSER_INIT = 'parser.initialised';

    const EVENT_PAGE_PARSING = 'page.parsing';

    const EVENT_PAGE_PARSED = 'page.parsed';

    const TOKEN_REDIRECT = 'redirect';

    /**
     * @var string
     */
    private $filter;

    /**
     * @var string
     */
    private $srcDir;

    /**
     * @var array
     */
    private $metaConfig;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var PageParser
     */
    private $pageParser;

    /**
     * @var Htaccess
     */
    private $htaccess;

    /**
     * SiteParser constructor.
     *
     * @param string          $srcDir
     * @param EventDispatcher $eventDispatcher
     * @param PageParser      $pageParser
     * @param Htaccess        $htaccess
     * @param array           $metaConfig
     */
    public function __construct(
        string $srcDir,
        EventDispatcher $eventDispatcher,
        PageParser $pageParser,
        Htaccess $htaccess,
        array $metaConfig = []
    ) {
        $this->srcDir = $srcDir;
        $this->eventDispatcher = $eventDispatcher;
        $this->pageParser = $pageParser;
        $this->htaccess = $htaccess;
        $this->metaConfig = $metaConfig;
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

            foreach ($fileContents as $route => $config) {
                if (count($routes) && !in_array($route, $routes)) {
                    continue;
                }

                $this->loadPage($site, $route, $config);
            }
        }

        return $site;
    }

    /**
     * @param Site   $site
     * @param string $route
     * @param array  $config
     */
    private function loadPage(Site $site, string $route, array $config) {
        if (isset($config[self::TOKEN_REDIRECT])) {
            $this->htaccess->addRedirect($route, $config[self::TOKEN_REDIRECT]);

            return;
        }

        $page = new Page($route, $config, $this->createMeta());
        $site->addPage($page);
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
        $this->eventDispatcher->dispatch(self::EVENT_PARSER_INIT, Event::create(['site' => $site]));

        foreach ($site as $page) {
            $this->eventDispatcher->dispatch(self::EVENT_PAGE_PARSING, Event::create(['page' => $page]));

            $this->pageParser->validate($page);
            $pages = $this->pageParser->parseAdapters($page, $filterValue);

            /** @var Page $entryPage */
            foreach ($pages as $entryPage) {
                $blanket[$entryPage->getId()] = $this->pageParser->parsePage($entryPage);
            }

            $this->eventDispatcher->dispatch(self::EVENT_PAGE_PARSED, Event::create(['page' => $page]));
        }

        return $blanket;
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
     * @return Meta
     */
    private function createMeta() : Meta {
        $meta = new Meta();

        foreach ($this->metaConfig as $name => $value) {
            $meta->name($name, $value);
        }

        return $meta;
    }
}

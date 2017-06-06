<?php

namespace Brendt\Stitcher\Parser\Site;

use Pageon\Html\Meta\Meta;
use Brendt\Stitcher\Event\Event;
use Brendt\Stitcher\Exception\InvalidSiteException;
use Brendt\Stitcher\Exception\TemplateNotFoundException;
use Brendt\Stitcher\Site\Http\Htaccess;
use Brendt\Stitcher\Site\Page;
use Brendt\Stitcher\Site\Site;
use Pageon\Pcntl\Manager;
use Pageon\Pcntl\PageRenderProcess;
use Pageon\Pcntl\ThreadHandlerCollection;
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
     * @var string
     */
    private $publicDir;

    /**
     * @var bool
     */
    private $async;

    /**
     * SiteParser constructor.
     *
     * @param string          $srcDir
     * @param string          $publicDir
     * @param bool            $async
     * @param EventDispatcher $eventDispatcher
     * @param PageParser      $pageParser
     * @param Htaccess        $htaccess
     * @param array           $metaConfig
     */
    public function __construct(
        string $srcDir,
        string $publicDir,
        bool $async,
        EventDispatcher $eventDispatcher,
        PageParser $pageParser,
        Htaccess $htaccess,
        array $metaConfig = []
    ) {
        $this->srcDir = $srcDir;
        $this->publicDir = $publicDir;
        $this->eventDispatcher = $eventDispatcher;
        $this->pageParser = $pageParser;
        $this->htaccess = $htaccess;
        $this->metaConfig = $metaConfig;
        $this->async = $async;
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
     * @throws TemplateNotFoundException
     */
    public function parse($routes = [], string $filterValue = null) {
        $manager = extension_loaded('pcntl') && $this->async ? new Manager($this->eventDispatcher) : null;
        $threadHandlerCollection = new ThreadHandlerCollection();

        $site = $this->loadSite((array) $routes);
        $this->eventDispatcher->dispatch(self::EVENT_PARSER_INIT, Event::create(['site' => $site]));

        foreach ($site as $page) {
            $this->eventDispatcher->dispatch(self::EVENT_PAGE_PARSING, Event::create(['page' => $page]));

            $pageRenderProcess = new PageRenderProcess($this->pageParser, $page, $this->publicDir, $filterValue);

            if ($manager) {
                $threadHandlerCollection[] = $manager->async($pageRenderProcess);
            } else {
                $event = $pageRenderProcess->execute();
                $this->eventDispatcher->dispatch($event->getEventHook(), $event);
            }
        }

        if ($manager) {
            $manager->wait($threadHandlerCollection);
        }
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

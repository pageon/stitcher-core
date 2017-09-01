<?php

namespace Brendt\Stitcher\Parser\Site;

use Brendt\Stitcher\Lib\Browser;
use Brendt\Stitcher\Site\Seo\SiteMap;
use Pageon\Html\Meta\Meta;
use Brendt\Stitcher\Event\Event;
use Brendt\Stitcher\Exception\InvalidSiteException;
use Brendt\Stitcher\Exception\TemplateNotFoundException;
use Brendt\Stitcher\Site\Http\Htaccess;
use Brendt\Stitcher\Site\Page;
use Brendt\Stitcher\Site\Site;
use Pageon\Pcntl\Manager;
use Pageon\Pcntl\PageRenderProcess;
use Pageon\Pcntl\ProcessCollection;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class SiteParser
{
    const EVENT_PARSER_INIT = 'parser.initialised';
    const EVENT_PAGE_PARSING = 'page.parsing';
    const EVENT_PAGE_PARSED = 'page.parsed';
    const TOKEN_REDIRECT = 'redirect';

    private $browser;
    private $filter;
    private $metaConfig;
    private $eventDispatcher;
    private $pageParser;
    private $htaccess;
    private $async;
    private $environment;
    private $siteMap;

    public function __construct(
        Browser $browser,
        string $environment,
        bool $async,
        EventDispatcher $eventDispatcher,
        PageParser $pageParser,
        Htaccess $htaccess,
        SiteMap $siteMap,
        array $metaConfig = []
    ) {
        $this->browser = $browser;
        $this->eventDispatcher = $eventDispatcher;
        $this->pageParser = $pageParser;
        $this->htaccess = $htaccess;
        $this->metaConfig = $metaConfig;
        $this->async = $async;
        $this->environment = $environment;
        $this->siteMap = $siteMap;
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
    public function loadSite(array $routes = []) : Site
    {
        /** @var SplFileInfo[] $files */
        $files = $this->browser->src()->path('site')->name('/.*\.yml|.*\.yaml/')->files();
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
    private function loadPage(Site $site, string $route, array $config)
    {
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
     * @return array
     * @throws TemplateNotFoundException
     */
    public function parse($routes = [], string $filterValue = null)
    {
        $blanket = [];
        $manager = extension_loaded('pcntl') && $this->async ? new Manager() : null;
        $processCollection = new ProcessCollection();

        $site = $this->loadSite((array) $routes);
        $this->eventDispatcher->dispatch(self::EVENT_PARSER_INIT, Event::create(['site' => $site]));

        foreach ($site as $page) {
            $pageRenderProcess = $this->createPageRenderProcess($page, $filterValue);
            $this->eventDispatcher->dispatch(self::EVENT_PAGE_PARSING, Event::create(['page' => $page]));

            if ($manager) {
                $pageRenderProcess->setAsync();
                $processCollection[] = $manager->async($pageRenderProcess);
            } else {
                $output = $pageRenderProcess->execute();
                $blanket += $output;
                $pageRenderProcess->triggerSuccess(array_keys($output));
            }
        }

        if ($manager) {
            $manager->wait($processCollection);
        }

        return $blanket;
    }

    /**
     * Create a page render process
     *
     * @param Page        $page
     * @param string|null $filterValue
     *
     * @return PageRenderProcess
     */
    private function createPageRenderProcess(Page $page, string $filterValue = null) : PageRenderProcess
    {
        $pageRenderProcess = new PageRenderProcess($this->pageParser, $page, $this->browser->getPublicDir(), $filterValue);
        $pageRenderProcess->setEnvironment($this->environment);

        $pageRenderProcess->onSuccess(function ($pageIds) use ($page) {
            if ($this->siteMap->isEnabled()) {
                foreach ($pageIds as $pageId) {
                    $this->siteMap->addPath($pageId);
                }
            }

            $this->eventDispatcher->dispatch(SiteParser::EVENT_PAGE_PARSED, Event::create(['pageId' => $page->getId()]));
        });

        return $pageRenderProcess;
    }

    /**
     * @param string $filter
     *
     * @return SiteParser
     */
    public function setFilter(string $filter) : SiteParser
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * @return Meta
     */
    private function createMeta() : Meta
    {
        $meta = new Meta();

        foreach ($this->metaConfig as $name => $value) {
            $meta->name($name, $value);
        }

        return $meta;
    }
}

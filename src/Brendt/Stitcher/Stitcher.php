<?php

namespace Brendt\Stitcher;

use Brendt\Stitcher\Exception\TemplateNotFoundException;
use Brendt\Stitcher\Parser\Site\SiteParser;
use Brendt\Stitcher\Site\Http\Htaccess;
use Brendt\Stitcher\Site\Seo\SiteMap;
use Brendt\Stitcher\Site\Site;
use Symfony\Component\Filesystem\Filesystem;

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
     * @var array
     */
    private $cdn;

    /**
     * @var bool
     */
    private $cdnCache;

    /**
     * @var SiteParser
     */
    private $siteParser;

    /**
     * @var Htaccess
     */
    private $htaccess;

    /**
     * @var SiteMap
     */
    private $siteMap;

    /**
     * @param string     $srcDir
     * @param string     $publicDir
     * @param string     $templateDir
     * @param array      $cdn
     * @param bool       $cdnCache
     * @param SiteParser $siteParser
     * @param Htaccess   $htaccess
     * @param SiteMap    $siteMap
     */
    public function __construct(
        $srcDir,
        $publicDir,
        $templateDir,
        array $cdn,
        bool $cdnCache,
        SiteParser $siteParser,
        Htaccess $htaccess,
        SiteMap $siteMap
    ) {
        $this->srcDir = $srcDir;
        $this->publicDir = $publicDir;
        $this->templateDir = $templateDir;
        $this->cdn = $cdn;
        $this->cdnCache = $cdnCache;
        $this->siteParser = $siteParser;
        $this->htaccess = $htaccess;
        $this->siteMap = $siteMap;
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
     * @see \Brendt\Stitcher\Application\DevController::run()
     * @see \Brendt\Stitcher\Adapter\CollectionAdapter::transform()
     */
    public function stitch($routes = [], string $filterValue = null) : array {
        if ($filterValue === null) {
            $this->htaccess->clearPageBlocks();
        }

        $this->prepareCdn();

        return $this->siteParser->parse((array) $routes, $filterValue);
    }

    /**
     * @param array $routes
     *
     * @return Site
     */
    public function loadSite(array $routes = []) : Site {
        return $this->siteParser->loadSite($routes);
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
     * Save .htaccess
     *
     * @return Stitcher
     */
    public function saveHtaccess() : Stitcher {
        $fs = new Filesystem();
        $fs->dumpFile("{$this->publicDir}/.htaccess", $this->htaccess->parse());

        return $this;
    }

    /**
     * Save the sitemap if enabled
     *
     * @return Stitcher
     */
    public function saveSitemap() : Stitcher {
        if (!$this->siteMap->isEnabled()) {
            return $this;
        }

        $fs = new Filesystem();
        $fs->dumpFile("{$this->publicDir}/sitemap.xml", $this->siteMap->render());

        return $this;
    }

    /**
     * Parse CDN resources and libraries
     */
    public function prepareCdn() {
        $fs = new Filesystem();

        foreach ($this->cdn as $resource) {
            $resource = trim($resource, '/');
            $publicResourcePath = "{$this->publicDir}/{$resource}";

            if ($this->cdnCache && $fs->exists($publicResourcePath)) {
                continue;
            }

            $sourceResourcePath = "{$this->srcDir}/{$resource}";
            if (is_dir($sourceResourcePath)) {
                $fs->mirror($sourceResourcePath, $publicResourcePath);
            } else {
                $fs->copy($sourceResourcePath, $publicResourcePath, true);
            }
        }
    }
}



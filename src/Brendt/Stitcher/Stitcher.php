<?php

namespace Brendt\Stitcher;

use Brendt\Stitcher\Exception\TemplateNotFoundException;
use Brendt\Stitcher\Site\Site;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Filesystem\Filesystem;
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
        'environment'          => 'development',
        'directories.src'      => './src',
        'directories.public'   => './public',
        'directories.cache'    => './.cache',
        'directories.htaccess' => './public/.htaccess',
        'meta'                 => [],
        'minify'               => false,
        'engines.template'     => 'smarty',
        'engines.image'        => 'gd',
        'engines.optimizer'    => true,
        'engines.async'        => true,
        'cdn'                  => [],
        'caches.image'         => true,
        'caches.cdn'           => true,
        'optimizer.options'    => [],
    ];

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
     * @var SiteParser
     */
    private $siteParser;

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

        $configFile = Config::getConfigFile($configPath);
        $parsedConfig = Yaml::parse($configFile->getContents());
        $parsedConfig = Config::parseImports($parsedConfig);

        $config = array_merge(
            self::$configDefaults,
            $parsedConfig,
            Config::flatten($parsedConfig),
            $defaultConfig
        );

        $config['directories.template'] = $config['directories.template'] ?? $config['directories.src'];

        foreach ($config as $key => $value) {
            self::$container->setParameter($key, $value);
        }

        $srcDir = $config['directories.src'] ?? null;
        $publicDir = $config['directories.public'] ?? null;
        $templateDir = $config['directories.template'] ?? null;

        $stitcher = new self($srcDir, $publicDir, $templateDir);
        self::$container->set('stitcher', $stitcher);

        $serviceLoader = new YamlFileLoader(self::$container, new FileLocator(__DIR__));
        $serviceLoader->load(__DIR__ . '/../../services.yml');

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
    public function stitch($routes = [], string $filterValue = null) : array {
        /** @var SiteParser $siteParser */
        $siteParser = self::get('parser.site');

        $this->prepareCdn();

        return $siteParser->parse($routes, $filterValue);
    }

    /**
     * @param array $routes
     *
     * @return Site
     */
    public function loadSite(array $routes = []) : Site {
        /** @var SiteParser $siteParser */
        $siteParser = self::get('parser.site');

        return $siteParser->loadSite($routes);
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
     * Parse CDN resources and libraries
     */
    public function prepareCdn() {
        $cdn = (array) self::getParameter('cdn');
        $enableCache = self::getParameter('caches.cdn');
        $fs = new Filesystem();

        foreach ($cdn as $resource) {
            $resource = trim($resource, '/');
            $publicResourcePath = "{$this->publicDir}/{$resource}";

            if ($enableCache && $fs->exists($publicResourcePath)) {
                continue;
            }

            $sourceResourcePath = "{$this->srcDir}/{$resource}";
            $fs->copy($sourceResourcePath, $publicResourcePath, true);
        }
    }

}



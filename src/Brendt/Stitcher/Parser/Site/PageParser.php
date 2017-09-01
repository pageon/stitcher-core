<?php

namespace Brendt\Stitcher\Parser\Site;

use Brendt\Stitcher\Exception\TemplateNotFoundException;
use Brendt\Stitcher\Factory\AdapterFactory;
use Brendt\Stitcher\Factory\HeaderCompilerFactory;
use Brendt\Stitcher\Factory\ParserFactory;
use Brendt\Stitcher\Factory\TemplateEngineFactory;
use Brendt\Stitcher\Lib\Browser;
use Brendt\Stitcher\Site\Meta\MetaCompiler;
use Brendt\Stitcher\Site\Page;
use Brendt\Stitcher\Template\TemplatePlugin;
use Symfony\Component\Finder\SplFileInfo;

class PageParser
{
    private $browser;
    private $parserFactory;
    private $metaCompiler;
    private $templatePlugin;
    private $adapterFactory;
    private $headerCompiler;
    private $templateEngine;
    private $templates;

    public function __construct(
        Browser $browser,
        AdapterFactory $adapterFactory,
        ParserFactory $parserFactory,
        HeaderCompilerFactory $headerCompilerFactory,
        TemplateEngineFactory $templateEngineFactory,
        TemplatePlugin $templatePlugin,
        MetaCompiler $metaCompiler
    ) {
        $this->browser = $browser;
        $this->adapterFactory = $adapterFactory;
        $this->parserFactory = $parserFactory;
        $this->metaCompiler = $metaCompiler;
        $this->templatePlugin = $templatePlugin;

        $this->templateEngine = $templateEngineFactory->getDefault();
        $this->templates = $this->loadTemplates();
        $this->headerCompiler = $headerCompilerFactory->getHeaderCompilerByEnvironment();
    }

    /**
     * Load all templates from either the `directories.template` directory. Depending on the configured template
     * engine, set with `engines.template`; .html or .tpl files will be loaded.
     *
     * @return SplFileInfo[]
     */
    public function loadTemplates()
    {
        $templateExtensions = $this->templateEngine->getTemplateExtensions();
        $templateExtensionsRegex = '/\.(' . implode('|', $templateExtensions) . ')/';

        /** @var SplFileInfo[] $files */
        $files = $this->browser->template()->name($templateExtensionsRegex)->files();
        $templates = [];

        foreach ($files as $file) {
            $id = preg_replace($templateExtensionsRegex, '', $file->getRelativePathname());
            $templates[$id] = $file;
        }

        return $templates;
    }

    public function parsePage(Page $page) : string
    {
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
    public function parseAdapters(Page $page, $entryId = null)
    {
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
    public function parseVariables(Page $page)
    {
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

    public function validate(Page $page)
    {
        $templateIsset = isset($this->templates[$page->getTemplatePath()]);

        if (!$templateIsset) {
            if ($template = $page->getTemplatePath()) {
                throw new TemplateNotFoundException("Template {$template} not found.");
            } else {
                throw new TemplateNotFoundException('No template was set.');
            }
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
    private function getData($value)
    {
        $parser = $this->parserFactory->getByFileName($value);

        if (!$parser) {
            return $value;
        }

        return $parser->parse($value);
    }
}

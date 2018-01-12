<?php

namespace Stitcher\Task;

use Stitcher\Page\Page;
use Stitcher\Page\PageParser;
use Stitcher\Page\PageRenderer;
use Stitcher\Task;
use Symfony\Component\Filesystem\Filesystem;

abstract class AbstractParse implements Task
{
    protected $outputDirectory;
    protected $configurationFile;
    protected $pageParser;
    protected $pageRenderer;

    public function __construct(
        string $outputDirectory,
        string $configurationFile,
        PageParser $pageParser,
        PageRenderer $pageRenderer
    ) {
        $this->outputDirectory = rtrim($outputDirectory, '/');
        $this->configurationFile = $configurationFile;
        $this->pageParser = $pageParser;
        $this->pageRenderer = $pageRenderer;
    }

    /**
     * @param string                      $outputDirectory
     * @param string                      $configurationFile
     * @param \Stitcher\Page\PageParser   $pageParser
     * @param \Stitcher\Page\PageRenderer $pageRenderer
     *
     * @return static
     */
    public static function make(
        string $outputDirectory,
        string $configurationFile,
        PageParser $pageParser,
        PageRenderer $pageRenderer
    ) {
        return new static($outputDirectory, $configurationFile, $pageParser, $pageRenderer);
    }

    protected function parsePageConfiguration($config): array
    {
        $pages = [];

        foreach ($config as $pageId => $pageConfiguration) {
            $pageConfiguration['id'] = $pageConfiguration['id'] ?? $pageId;

            $pages += $this->pageParser->parse($pageConfiguration)->toArray();
        }

        return $pages;
    }

    protected function renderPages($pages): void
    {
        $fs = new Filesystem();

        /**
         * @var string $pageId
         * @var Page   $page
         */
        foreach ($pages as $pageId => $page) {
            $fileName = $pageId === '/' ? 'index' : $pageId;

            $renderedPage = $this->pageRenderer->render($page);

            $fs->dumpFile("{$this->outputDirectory}/{$fileName}.html", $renderedPage);
        }
    }
}

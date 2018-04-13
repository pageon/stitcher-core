<?php

namespace Stitcher\Task;

use Pageon\Html\SiteMap;
use Stitcher\Page\Page;
use Stitcher\Page\PageParser;
use Stitcher\Page\PageRenderer;
use Stitcher\Task;
use Symfony\Component\Filesystem\Filesystem;

abstract class AbstractParse implements Task
{
    /** @var string */
    protected $publicDirectory;

    /** @var string */
    protected $configurationFile;

    /** @var \Stitcher\Page\PageParser  */
    protected $pageParser;

    /** @var \Stitcher\Page\PageRenderer */
    protected $pageRenderer;

    /** @var Task[] */
    protected $tasks = [];

    /** @var \Pageon\Html\SiteMap */
    protected $siteMap;

    public function __construct(
        string $publicDirectory,
        string $configurationFile,
        PageParser $pageParser,
        PageRenderer $pageRenderer,
        SiteMap $siteMap
    ) {
        $this->publicDirectory = rtrim($publicDirectory, '/');
        $this->configurationFile = $configurationFile;
        $this->pageParser = $pageParser;
        $this->pageRenderer = $pageRenderer;
        $this->siteMap = $siteMap;
    }

    public static function make(
        string $publicDirectory,
        string $configurationFile,
        PageParser $pageParser,
        PageRenderer $pageRenderer,
        SiteMap $siteMap
    ) : self {
        return new static(
            $publicDirectory,
            $configurationFile,
            $pageParser,
            $pageRenderer,
            $siteMap
        );
    }

    public function addSubTask(Task $task)
    {
        $this->tasks[] = $task;

        return $this;
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

            $this->siteMap->addPath($pageId);

            $fs->dumpFile(
                "{$this->publicDirectory}/{$fileName}.html",
                $renderedPage
            );
        }
    }

    protected function executeSubTasks(): void
    {
        foreach ($this->tasks as $task) {
            $task->execute();
        }
    }
}

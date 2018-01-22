<?php

namespace Stitcher\Task;

use Stitcher\Page\Page;
use Stitcher\Page\PageParser;
use Stitcher\Page\PageRenderer;
use Stitcher\Task;
use Symfony\Component\Filesystem\Filesystem;

abstract class AbstractParse implements Task
{
    protected $publicDirectory;
    protected $configurationFile;
    protected $pageParser;
    protected $pageRenderer;

    /** @var Task[] */
    protected $tasks = [];

    public function __construct(
        string $publicDirectory,
        string $configurationFile,
        PageParser $pageParser,
        PageRenderer $pageRenderer
    ) {
        $this->publicDirectory = rtrim($publicDirectory, '/');
        $this->configurationFile = $configurationFile;
        $this->pageParser = $pageParser;
        $this->pageRenderer = $pageRenderer;
    }

    public static function make(
        string $publicDirectory,
        string $configurationFile,
        PageParser $pageParser,
        PageRenderer $pageRenderer
    ) : self {
        return new static(
            $publicDirectory,
            $configurationFile,
            $pageParser,
            $pageRenderer
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

            $fs->dumpFile("{$this->publicDirectory}/{$fileName}.html", $renderedPage);
        }
    }

    protected function executeSubTasks(): void
    {
        foreach ($this->tasks as $task) {
            $task->execute();
        }
    }
}

<?php

namespace Stitcher\Command;

use Stitcher\Command;
use Stitcher\File;
use Stitcher\Page\Page;
use Stitcher\Page\PageParser;
use Stitcher\Page\PageRenderer;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class Parse implements Command
{
    private $outputDirectory;
    private $configurationFile;
    private $pageParser;
    private $pageRenderer;

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

    public static function make(
        string $outputDirectory,
        string $configurationFile,
        PageParser $pageParser,
        PageRenderer $pageRenderer
    ): Parse {
        return new self($outputDirectory, $configurationFile, $pageParser, $pageRenderer);
    }

    public function execute(): void
    {
        $parsedConfiguration = Yaml::parse(File::read($this->configurationFile));

        $pages = $this->parsePageConfiguration($parsedConfiguration);

        $this->renderPages($pages);
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

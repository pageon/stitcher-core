<?php

namespace Stitcher\Page;

use Illuminate\Support\Collection;
use Stitcher\Exception\InvalidConfiguration;
use Stitcher\Page\Adapter\AdapterFactory;

class PageParser
{
    /** @var PageFactory */
    private $pageFactory;

    /** @var AdapterFactory */
    private $adapterFactory;

    /** @var Page */
    private $currentPage;

    public function __construct(PageFactory $pageFactory, AdapterFactory $adapterFactory)
    {
        $this->pageFactory = $pageFactory;
        $this->adapterFactory = $adapterFactory;
    }

    public static function make(PageFactory $factory, AdapterFactory $adapterFactory): PageParser
    {
        return new self($factory, $adapterFactory);
    }

    public function parse($pageConfiguration): PageCollection
    {
        $result = new PageCollection();

        $pageEntries = $this->parseAdapterConfiguration($pageConfiguration);

        foreach ($pageEntries as $pageEntry) {
            $page = $this->parsePage($pageEntry);

            $this->setCurrentPage($page);

            $result[$page->id()] = $page;
        }

        return $result;
    }

    public function getCurrentPage(): ?Page
    {
        return $this->currentPage;
    }

    private function setCurrentPage(Page $page): PageParser
    {
        $this->currentPage = $page;

        return $this;
    }

    private function parseAdapterConfiguration(array $pageConfiguration): array
    {
        $pageEntries = [$pageConfiguration];

        $adapters =
            $pageConfiguration['config']
            ?? $pageConfiguration['adapters']
            ?? [];

        foreach ($adapters as $adapterType => $adapterConfiguration) {
            $adapter = $this->adapterFactory->create(
                $adapterType,
                $adapterConfiguration
            );

            if (! $adapter) {
                throw InvalidConfiguration::adapterNotFound($adapterType);
            }

            $pageEntries = $this->adaptPageEntries($pageEntries, $adapter);
        }

        return $pageEntries;
    }

    private function adaptPageEntries(array $pageEntries, Adapter $adapter): array
    {
        $adaptedPageEntries = [];

        foreach ($pageEntries as $pageToTransform) {
            $adaptedPageEntries = array_merge(
                $adaptedPageEntries,
                $adapter->transform($pageToTransform)
            );
        }

        $pageEntries = $adaptedPageEntries;

        return $pageEntries;
    }

    private function parsePage($inputConfiguration): Page
    {
        return $this->pageFactory->create($inputConfiguration);
    }
}

<?php

namespace Stitcher\Page;

use Illuminate\Support\Collection;
use Stitcher\Page\Adapter\AdapterFactory;

class PageParser
{
    private $pageFactory;
    private $adapterFactory;

    public function __construct(PageFactory $pageFactory, AdapterFactory $adapterFactory)
    {
        $this->pageFactory = $pageFactory;
        $this->adapterFactory = $adapterFactory;
    }

    public static function make(PageFactory $factory, AdapterFactory $adapterFactory): PageParser
    {
        return new self($factory, $adapterFactory);
    }

    public function parse($pageConfiguration): Collection
    {
        $result = [];

        $pageEntries = $this->parseAdapterConfiguration($pageConfiguration);

        foreach ($pageEntries as $pageEntry) {
            $page = $this->parsePage($pageEntry);

            $result[$page->id()] = $page;
        }

        return collect($result);
    }

    private function parseAdapterConfiguration(array $pageConfiguration): array
    {
        $pageEntries = [$pageConfiguration];

        $adapterConfigurations = $pageConfiguration['config'] ?? $pageConfiguration['adapters'] ?? [];

        foreach ($adapterConfigurations as $adapterType => $adapterConfiguration) {
            $adapter = $this->adapterFactory->create($adapterType, $adapterConfiguration);

            $adaptedPageEntries = [];

            foreach ($pageEntries as $pageToTransform) {
                $adaptedPageEntries = array_merge($adaptedPageEntries, $adapter->transform($pageToTransform));
            }

            $pageEntries = $adaptedPageEntries;
        }

        return $pageEntries;
    }

    private function parsePage($inputConfiguration): Page
    {
        return $this->pageFactory->create($inputConfiguration);
    }
}

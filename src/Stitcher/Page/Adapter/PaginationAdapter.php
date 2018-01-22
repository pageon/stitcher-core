<?php

namespace Stitcher\Page\Adapter;

use Stitcher\Exception\InvalidPaginationAdapter;
use Stitcher\Page\Adapter;
use Stitcher\Configureable;
use Stitcher\Variable\VariableParser;

class PaginationAdapter implements Adapter, Configureable
{
    protected $variableParser;
    protected $variable;
    protected $perPage;
    protected $parameter;

    public function __construct(array $adapterConfiguration, VariableParser $variableParser)
    {
        if (! $this->isValidConfiguration($adapterConfiguration)) {
            throw InvalidPaginationAdapter::create();
        }

        $this->variable = $adapterConfiguration['variable'];
        $this->parameter = $adapterConfiguration['parameter'];
        $this->perPage = $adapterConfiguration['perPage'] ?? 12;
        $this->variableParser = $variableParser;
    }

    public static function make(array $adapterConfiguration, VariableParser $variableParser): PaginationAdapter
    {
        return new self($adapterConfiguration, $variableParser);
    }

    public function transform(array $pageConfiguration): array
    {
        $paginationPageConfiguration = [];
        $entries = $this->getEntries($pageConfiguration);
        $pageCount = (int) ceil(count($entries) / $this->perPage);

        for ($pageIndex = 1; $pageIndex <= $pageCount; $pageIndex++) {
            $entriesForPage = array_splice($entries, 0, $this->perPage);

            $entryConfiguration = $this->createPageConfiguration(
                $pageConfiguration,
                $entriesForPage,
                $pageIndex,
                $pageCount
            );

            $paginationPageConfiguration[$entryConfiguration['id']] = $entryConfiguration;
        }

        return $paginationPageConfiguration;
    }

    public function isValidConfiguration($subject): bool
    {
        return is_array($subject) && isset($subject['variable']) && isset($subject['parameter']);
    }

    protected function getEntries(array $pageConfiguration): ?array
    {
        $variable = $pageConfiguration['variables'][$this->variable] ?? null;
        $entries = $this->variableParser->parse($variable)['entries']
            ?? $this->variableParser->parse($variable)
            ?? $variable;

        return $entries;
    }

    protected function createPageConfiguration(
        array $entryConfiguration,
        array $entriesForPage,
        int $pageIndex,
        int $pageCount
    ): array {
        $pageId = rtrim($entryConfiguration['id'], '/');
        $paginatedId = $this->createPaginatedUrl($pageId, $pageIndex);

        $entryConfiguration['id'] = $paginatedId;
        $entryConfiguration['variables'][$this->variable] = $entriesForPage;

        $paginationVariable = $this->createPaginationVariable($pageId, $pageIndex, $pageCount);
        $entryConfiguration['variables']['_pagination'] = $paginationVariable;

        unset($entryConfiguration['config']['pagination']);

        return $entryConfiguration;
    }

    protected function createPaginationVariable(string $pageId, int $pageIndex, int $pageCount): array
    {
        return [
            'current'  => $pageIndex,
            'previous' => $this->createPreviousPagination($pageId, $pageIndex),
            'next'     => $this->createNextPagination($pageId, $pageIndex, $pageCount),
            'pages'    => $pageCount,
        ];
    }

    protected function createPreviousPagination(string $pageId, int $pageIndex): ?array
    {
        if ($pageIndex <= 1) {
            return null;
        }

        $previous = $pageIndex - 1;

        return [
            'url'   => $this->createPaginatedUrl($pageId, $previous),
            'index' => $previous,
        ];
    }

    protected function createNextPagination(string $pageId, int $pageIndex, int $pageCount): ?array
    {
        if ($pageIndex >= $pageCount) {
            return null;
        }

        $next = $pageIndex + 1;

        return [
            'url'   => $this->createPaginatedUrl($pageId, $next),
            'index' => $next,
        ];
    }

    protected function createPaginatedUrl(string $pageId, int $index): string
    {
        return str_replace('{' .$this->parameter. '}', $index, $pageId);
    }
}

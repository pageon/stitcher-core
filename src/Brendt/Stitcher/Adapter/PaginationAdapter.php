<?php

namespace Brendt\Stitcher\Adapter;

use Brendt\Stitcher\Exception\ConfigurationException;
use Brendt\Stitcher\Exception\VariableNotFoundException;
use Brendt\Stitcher\Factory\AdapterFactory;
use Brendt\Stitcher\Site\Page;

/**
 * The PaginationAdapter takes a page with a collection of entries and generates pagination for that collection.
 *
 * Sample configuration:
 *
 *  /examples:
 *      template: examples/overview
 *      data:
 *          collection: collection.yml
 *      adapters:
 *      pagination:
 *          variable: collection
 *          entriesPerPage: 4
 */
class PaginationAdapter extends AbstractAdapter
{
    private $pageCount = null;
    private $variable = null;
    private $entriesPerPage = null;
    private $entries = [];

    public function transformPage(Page $page, $filter = null) : array
    {
        $this->loadConfig($page);

        $index = 0;
        $result = [];

        while ($index < $this->pageCount) {
            $pageEntries = array_splice($this->entries, 0, $this->entriesPerPage);
            $pageIndex = $index + 1;

            if ($filter === null || $pageIndex === (int) $filter) {
                $paginatedPage = $this->createPaginatedPage($page, $pageIndex, $pageEntries);
                $result[$paginatedPage->getId()] = $paginatedPage;
            }

            $index += 1;
        }

        $this->createMainPage(rtrim($page->getId(), '/'), $result);

        return $result;
    }

    private function createPaginatedPage(Page $page, int $pageIndex, array $pageEntries) : Page
    {
        $url = "{$page->getId()}/page-{$pageIndex}";
        $pagination = $this->createPagination($page->getId(), $pageIndex);
        $paginatedPage = clone $page;

        $paginatedPage
            ->removeAdapter(AdapterFactory::PAGINATION_ADAPTER)
            ->setVariableValue($this->variable, $pageEntries)
            ->setVariableIsParsed($this->variable)
            ->setVariableValue('pagination', $pagination)
            ->setVariableIsParsed('pagination')
            ->setId($url);

        return $paginatedPage;
    }

    private function createMainPage(string $pageId, array &$result)
    {
        $firstPage = reset($result);

        if (!$firstPage) {
            return;
        }

        $mainPage = clone $firstPage;
        $mainPage->setId($pageId);
        $result[$pageId] = $mainPage;
    }

    private function createPagination($pageId, $pageIndex)
    {
        $next = count($this->entries) ? $pageIndex + 1 : null;
        $nextUrl = $next ? "{$pageId}/page-{$next}" : null;
        $previous = $pageIndex > 1 ? $pageIndex - 1 : null;
        $previousUrl = $previous ? "{$pageId}/page-{$previous}" : null;

        return [
            'current'  => $pageIndex,
            'previous' => $previous ? [
                'url'   => $previousUrl,
                'index' => $previous,
            ] : null,
            'next'     => $next ? [
                'url'   => $nextUrl,
                'index' => $next,
            ] : null,
            'pages'    => $this->pageCount,
        ];
    }

    private function loadConfig(Page $page)
    {
        $config = $page->getAdapterConfig(AdapterFactory::PAGINATION_ADAPTER);

        if (!isset($config['variable'])) {
            throw new ConfigurationException('The configuration entry `variable` is required when using the Pagination adapter');
        }

        $this->variable = $config['variable'] ?? null;

        if (!$page->getVariable($this->variable)) {
            throw new VariableNotFoundException("Variable \"{$this->variable}\" was not set as a data variable for page \"{$page->getId()}\"");
        }

        $this->entries = (array) $this->getData($page->getVariable($this->variable));
        $this->entriesPerPage = (int) $config['entriesPerPage'] ?? 10;
        $this->pageCount = (int) ceil(count($this->entries) / $this->entriesPerPage);
    }
}

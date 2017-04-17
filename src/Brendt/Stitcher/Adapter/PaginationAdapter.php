<?php

namespace Brendt\Stitcher\Adapter;

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

    /**
     * @param Page $page
     * @param null $filter
     *
     * @return array|Page[]
     * @throws VariableNotFoundException
     */
    public function transformPage(Page $page, $filter = null) : array {
        $config = $page->getAdapterConfig(AdapterFactory::PAGINATION_ADAPTER);

        if (!isset($config['variable'])) {
            return [$page];
        }

        $variable = $config['variable'];

        if (!$source = $page->getVariable($variable)) {
            throw new VariableNotFoundException("Variable \"{$variable}\" was not set as a data variable for page \"{$page->getId()}\"");
        }

        $pageId = rtrim($page->getId(), '/');
        $entries = $this->getData($source);
        $entriesPerPage = isset($config['entriesPerPage']) ? $config['entriesPerPage'] : 10;
        $pageCount = (int) ceil(count($entries) / $entriesPerPage);

        $i = 0;
        $result = [];

        while ($i < $pageCount) {
            $pageEntries = array_splice($entries, 0, $entriesPerPage);
            $pageIndex = $i + 1;

            if ($filter && $pageIndex !== (int) $filter) {
                $i += 1;
                continue;
            }

            $url = "{$pageId}/page-{$pageIndex}";
            $pagination = $this->createPagination($pageId, $pageIndex, $pageCount, $entries);
            $entriesPage = clone $page;
            $entriesPage->parseMeta(['pagination' => $pagination]);

            $entriesPage
                ->removeAdapter(AdapterFactory::PAGINATION_ADAPTER)
                ->setVariableValue($variable, $pageEntries)
                ->setVariableIsParsed($variable)
                ->setVariableValue('pagination', $pagination)
                ->setVariableIsParsed('pagination')
                ->setId($url);

            $result[$url] = $entriesPage;
            $i += 1;
        }

        if ($firstPage = reset($result)) {
            $mainPage = clone $firstPage;
            $mainPage->setId($pageId);
            $result[$pageId] = $mainPage;
        }

        return $result;
    }

    /**
     * Create a pagination array.
     *
     * @param $pageId
     * @param $pageIndex
     * @param $pageCount
     * @param $entries
     *
     * @return array
     */
    protected function createPagination($pageId, $pageIndex, $pageCount, $entries) {
        $next = count($entries) ? $pageIndex + 1 : null;
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
            'pages'    => $pageCount,
        ];
    }
}

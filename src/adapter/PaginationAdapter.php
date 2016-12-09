<?php

namespace brendt\stitcher\adapter;

use brendt\stitcher\site\Page;
use brendt\stitcher\factory\AdapterFactory;

class PaginationAdapter extends AbstractAdapter {

    /**
     * @param Page $page
     * @param null $filter
     *
     * @return Page[]
     */
    public function transform(Page $page, $filter = null) {
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
        $amount = isset($config['amount']) ? $config['amount'] : 10;
        $pageCount = (int) ceil(count($entries) / $amount);

        $i = 0;
        $result = [];

        while ($i < $pageCount) {
            $pageEntries = array_splice($entries, 0, $amount);
            $pageIndex = $i + 1;

            if ($filter && $pageIndex !== (int) $filter) {
                $i += 1;
                continue;
            }

            $url = "{$pageId}/page-{$pageIndex}";
            $pagination = $this->createPagination($pageId, $pageIndex, $pageCount, $entries);
            $entriesPage = clone $page;
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
     * Create a pagination variable
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

<?php

namespace brendt\stitcher\adapter;

use brendt\stitcher\element\Page;
use brendt\stitcher\factory\AdapterFactory;

class PaginationAdapter extends AbstractAdapter {

    /**
     * @param Page $page
     * @param null $filter
     *
     * @return Page[]
     */
    public function transform(Page $page, $filter = null) {
        $config = $page->getAdapter(AdapterFactory::PAGINATION_ADAPTER);

        if (!isset($config['name'])) {
            return [$page];
        }

        $name = $config['name'];

        if (!$source = $page->getVariable($name)) {
            throw new VariableNotFoundException("Variable \"{$name}\" was not set as a data variable for page \"{$page->getId()}\"");
        }

        $entries = $this->getData($source);
        $perPage = isset($config['perPage']) ? $config['perPage'] : 10;
        $pageId = rtrim($page->getId(), '/');
        $result = [];

        $i = 0;
        $pageCount = (int) ceil(count($entries) / $perPage);
        while ($i < $pageCount) {

            $pageEntries = array_splice($entries, 0, $perPage);

            $pageIndex = $i * $perPage + 1;
            $url = "{$pageId}/{$pageIndex}";
            $entriesPage = clone $page;
            $pagination = [
                'current'  => $pageIndex,
                'previous' => $pageIndex > 1 ? $pageIndex - 1 : null,
                'next'     => count($entries) ? $pageIndex + 1 : null,
                'pages'    => $pageCount,
            ];

            $entriesPage
                ->clearAdapter(AdapterFactory::PAGINATION_ADAPTER)
                ->setVariable($name, $pageEntries)
                ->setParsedField($name)
                ->setVariable('pagination', $pagination)
                ->setParsedField('pagination')
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
}

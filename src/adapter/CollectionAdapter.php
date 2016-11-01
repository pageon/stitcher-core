<?php

namespace brendt\stitcher\adapter;

use brendt\stitcher\element\Page;
use brendt\stitcher\factory\AdapterFactory;

class CollectionAdapter extends AbstractAdapter {

    /**
     * @param Page  $page
     * @param mixed $filter
     *
     * @return Page[]
     */
    public function transform(Page $page, $filter = null) {
        $config = $page->getAdapter(AdapterFactory::COLLECTION_ADAPTER);

        if (!isset($config['field']) || !isset($config['name'])) {
            return [$page];
        }

        $field = $config['field'];
        $name = $config['name'];

        if (!$source = $page->getVariable($name)) {
            return [$page];
        }

        $entries = $this->getData($source);

        $result = [];
        $pageId = $page->getId();

        foreach ($entries as $entry) {
            if (!isset($entry[$field]) || ($filter && $entry[$field] !== $filter)) {
                continue;
            }

            $fieldValue = $entry[$field];
            $url = str_replace('{' . $field . '}', $fieldValue, $pageId);
            $entryPage = clone $page;

            $entryPage
                ->clearAdapter(AdapterFactory::COLLECTION_ADAPTER)
                ->setVariable($name, $entry)
                ->setParsedField($name)
                ->setId($url);

            $result[$url] = $entryPage;
        }

        return $result;
    }

}

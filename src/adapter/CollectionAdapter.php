<?php

namespace brendt\stitcher\adapter;

use brendt\stitcher\element\Page;
use brendt\stitcher\factory\AdapterFactory;

class CollectionAdapter extends AbstractAdapter {

    /**
     * @param Page $page
     *
     * @return Page[]
     */
    public function transform(Page $page) {
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
            if (!isset($entry[$field])) {
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

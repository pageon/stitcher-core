<?php

namespace brendt\stitcher\adapter;

use brendt\stitcher\element\Page;
use brendt\stitcher\exception\IdFieldNotFoundException;
use brendt\stitcher\exception\VariableNotFoundException;
use brendt\stitcher\factory\AdapterFactory;

class CollectionAdapter extends AbstractAdapter {

    /**
     * @param Page  $page
     * @param mixed $filter
     *
     * @return \brendt\stitcher\element\Page[]
     * @throws IdFieldNotFoundException
     * @throws VariableNotFoundException
     */
    public function transform(Page $page, $filter = null) {
        $config = $page->getAdapter(AdapterFactory::COLLECTION_ADAPTER);

        if (!isset($config['field']) || !isset($config['variable'])) {
            return [$page];
        }

        $variable = $config['variable'];

        if (!$source = $page->getVariable($variable)) {
            throw new VariableNotFoundException("Variable \"{$variable}\" was not set as a data variable for page \"{$page->getId()}\"");
        }

        $pageId = $page->getId();
        $entries = $this->getData($source);
        $field = $config['field'];

        if (strpos($pageId, '{' . $field . '}') === false) {
            throw new IdFieldNotFoundException("The field \"{{$field}}\" was not found in the URL \"{$page->getId()}\"");
        }

        $result = [];

        foreach ($entries as $entry) {
            if (!isset($entry[$field]) || ($filter && $entry[$field] !== $filter)) {
                continue;
            }

            $fieldValue = $entry[$field];

            $url = str_replace('{' . $field . '}', $fieldValue, $pageId);
            $entryPage = clone $page;

            $entryPage
                ->clearAdapter(AdapterFactory::COLLECTION_ADAPTER)
                ->setVariable($variable, $entry)
                ->setParsedField($variable)
                ->setId($url);

            $result[$url] = $entryPage;
        }

        return $result;
    }

}

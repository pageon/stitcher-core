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

        if (!isset($config['field']) || !isset($config['name'])) {
            return [$page];
        }

        $field = $config['field'];
        $name = $config['name'];

        if (!$source = $page->getVariable($name)) {
            throw new VariableNotFoundException("Variable \"{$name}\" was not set as a data variable for page \"{$page->getId()}\"");
        }

        $entries = $this->getData($source);
        $result = [];
        $pageId = $page->getId();

        if (strpos($pageId, '{' . $field . '}') === false) {
            throw new IdFieldNotFoundException("The field \"{{$field}}\" was not found in the URL \"{$page->getId()}\"");
        }

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

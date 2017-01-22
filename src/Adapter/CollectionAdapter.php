<?php

namespace Brendt\Stitcher\Adapter;

use Brendt\Stitcher\Exception\IdFieldNotFoundException;
use Brendt\Stitcher\Exception\VariableNotFoundException;
use Brendt\Stitcher\Factory\AdapterFactory;
use Brendt\Stitcher\Site\Page;

/**
 * The CollectionAdapter takes a page with a collection of entries, and generates a detail page for each entry in the
 * collection.
 *
 * Sample configuration:
 *
 *  /examples/{id}:
 *      template: examples/detail
 *      data:
 *          example: collection.yml
 *      adapters:
 *          collection:
 *              variable: example
 *              field: id
 *
 * @todo Rename `field` option to `urlVariable`
 */
class CollectionAdapter extends AbstractAdapter
{

    /**
     * @param Page  $page
     * @param mixed $filter
     *
     * @return Page[]
     * @throws IdFieldNotFoundException
     * @throws VariableNotFoundException
     */
    public function transform(Page $page, $filter = null) {
        $config = $page->getAdapterConfig(AdapterFactory::COLLECTION_ADAPTER);

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
                ->removeAdapter(AdapterFactory::COLLECTION_ADAPTER)
                ->setVariableValue($variable, $entry)
                ->setVariableIsParsed($variable)
                ->setId($url);

            $result[$url] = $entryPage;
        }

        return $result;
    }

}

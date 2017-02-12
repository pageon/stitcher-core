<?php

namespace Brendt\Stitcher\Adapter;

use Brendt\Stitcher\Exception\ConfigurationException;
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
     * @throws ConfigurationException
     * @throws IdFieldNotFoundException
     * @throws VariableNotFoundException
     */
    public function transform(Page $page, $filter = null) {
        $config = $page->getAdapterConfig(AdapterFactory::COLLECTION_ADAPTER);

        $this->validateConfig($config, $page);

        $variable = $config['variable'];
        $source = $page->getVariable($variable);
        $field = $config['field'];
        $pageId = $page->getId();
        $entries = $this->getData($source);

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

    /**
     * @param array $config
     * @param Page  $page
     *
     * @return void
     * @throws ConfigurationException
     * @throws IdFieldNotFoundException
     * @throws VariableNotFoundException
     */
    protected function validateConfig(array $config, Page $page) {
        if (!isset($config['field'], $config['variable'])) {
            throw new ConfigurationException('Both the configuration entry `field` and `variable` are required when using the Collection adapter.');
        }

        $variable = $config['variable'];

        if (!$page->getVariable($variable)) {
            throw new VariableNotFoundException("Variable \"{$variable}\" was not set as a data variable for page \"{$page->getId()}\"");
        }

        $field = $config['field'];
        $pageId = $page->getId();

        if (strpos($pageId, '{' . $field . '}') === false) {
            throw new IdFieldNotFoundException("The field \"{{$field}}\" was not found in the URL \"{$page->getId()}\"");
        }
    }

}

<?php

namespace Brendt\Stitcher\Adapter;

use Brendt\Html\Meta\Meta;
use Brendt\Stitcher\Exception\ConfigurationException;
use Brendt\Stitcher\Exception\IdFieldNotFoundException;
use Brendt\Stitcher\Exception\VariableNotFoundException;
use Brendt\Stitcher\Factory\AdapterFactory;
use Brendt\Stitcher\factory\ParserFactory;
use Brendt\Stitcher\Site\Meta\MetaCompiler;
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
 *          variableName: collection.yml
 *      adapters:
 *          variableName:
 *              variable: example
 *              field: id
 */
class CollectionAdapter extends AbstractAdapter
{
    /**
     * @var MetaCompiler
     */
    private $metaCompiler;

    public function __construct(ParserFactory $parserFactory, MetaCompiler $metaCompiler) {
        parent::__construct($parserFactory);

        $this->metaCompiler = $metaCompiler;
    }

    /**
     * @param Page $page
     * @param null $filter
     *
     * @return Page[]
     */
    public function transformPage(Page $page, $filter = null) : array {
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

            foreach ($entry as $entryVariableName => $entryVariableValue) {
                $this->metaCompiler->compilePageVariable($entryPage, $entryVariableName, $entryVariableValue);
            }

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

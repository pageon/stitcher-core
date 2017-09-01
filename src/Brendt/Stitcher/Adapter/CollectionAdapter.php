<?php

namespace Brendt\Stitcher\Adapter;

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
    private $metaCompiler;
    private $variable = null;
    private $field = null;
    private $entries = [];

    public function __construct(ParserFactory $parserFactory, MetaCompiler $metaCompiler)
    {
        parent::__construct($parserFactory);

        $this->metaCompiler = $metaCompiler;
    }

    public function transformPage(Page $page, $filter = null) : array
    {
        $this->loadConfig($page);
        $result = [];

        while ($entry = current($this->entries)) {
            if (isset($entry[$this->field]) && (!$filter || $entry[$this->field] === $filter)) {
                $entryPage = $this->createEntryPage($page, $entry);
                $result[$entryPage->getId()] = $entryPage;
            }

            next($this->entries);
        }

        return $result;
    }

    private function createEntryPage(Page $page, array $entry) : Page
    {
        $url = str_replace('{' . $this->field . '}', $entry[$this->field], $page->getId());
        $entryPage = Page::copy($page);
        $entryPageMeta = $entryPage->getMeta();
        $this->metaCompiler->setDefaultMeta($entryPageMeta);

        foreach ($entry as $entryVariableName => $entryVariableValue) {
            $this->metaCompiler->compilePageVariable($entryPage, $entryVariableName, $entryVariableValue);
        }

        $entryPage
            ->removeAdapter(AdapterFactory::COLLECTION_ADAPTER)
            ->setVariableValue($this->variable, $entry)
            ->setVariableIsParsed($this->variable)
            ->setId($url);

        $this->parseBrowseData($entryPage);

        return $entryPage;
    }

    private function parseBrowseData(Page $entryPage)
    {
        if ($entryPage->getVariable('browse')) {
            return;
        }

        $prev = prev($this->entries);

        if (!$prev) {
            reset($this->entries);
        } else {
            next($this->entries);
        }

        $next = next($this->entries);

        $entryPage->setVariableValue('browse', [
            'prev' => $prev,
            'next' => $next,
        ])->setVariableIsParsed('browse');

        prev($this->entries);
    }

    protected function loadConfig(Page $page)
    {
        $config = $page->getAdapterConfig(AdapterFactory::COLLECTION_ADAPTER);

        if (!isset($config['field'], $config['variable'])) {
            throw new ConfigurationException('Both the configuration entry `field` and `variable` are required when using the Collection adapter.');
        }

        $this->variable = $config['variable'];

        if (!$page->getVariable($this->variable)) {
            throw new VariableNotFoundException("Variable \"{$this->variable}\" was not set as a data variable for page \"{$page->getId()}\"");
        }

        $this->field = $config['field'];

        if (strpos($page->getId(), '{' . $this->field . '}') === false) {
            throw new IdFieldNotFoundException("The field \"{{$this->field}}\" was not found in the URL \"{$page->getId()}\"");
        }

        $this->entries = (array) $this->getData($page->getVariable($this->variable));
        reset($this->entries);
    }
}

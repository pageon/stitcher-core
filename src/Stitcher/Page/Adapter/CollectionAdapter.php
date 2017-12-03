<?php

namespace Stitcher\Page\Adapter;

use Stitcher\Adapter;
use Stitcher\Exception\InvalidConfiguration;
use Stitcher\Validatory;
use Stitcher\Variable\VariableParser;

class CollectionAdapter implements Adapter, Validatory
{
    private $parameter;
    private $variable;
    private $variableParser;

    public function __construct(array $adapterConfiguration, VariableParser $variableParser)
    {
        if (! $this->isValid($adapterConfiguration)) {
            throw InvalidConfiguration::invalidAdapterConfiguration('collection', '`variable` and `parameter`');
        }

        $this->variable = $adapterConfiguration['variable'];
        $this->parameter = $adapterConfiguration['parameter'];
        $this->variableParser = $variableParser;
    }

    public static function make(array $adapterConfiguration, VariableParser $variableParser)
    {
        return new self($adapterConfiguration, $variableParser);
    }

    public function transform(array $pageConfiguration): array
    {
        $entries = $this->getEntries($pageConfiguration);
        $collectionPageConfiguration = [];

        foreach ($entries as $entryId => $entry) {
            $entryConfiguration = $this->createEntryConfiguration($pageConfiguration, $entryId, $entry);

            $collectionPageConfiguration[$entryConfiguration['id']] = $entryConfiguration;
        }

        return $collectionPageConfiguration;
    }

    public function isValid($subject): bool
    {
        return is_array($subject) && isset($subject['variable']) && isset($subject['parameter']);
    }

    protected function getEntries($pageConfiguration): ?array
    {
        $variable = $pageConfiguration['variables'][$this->variable] ?? null;

        $entries = $this->variableParser->parse($variable) ?? $variable;

        return $entries;
    }

    protected function createEntryConfiguration(array $pageConfiguration, $entryId, $entry): array
    {
        $entryConfiguration = $pageConfiguration;
        $parsedEntryId = str_replace('{' . $this->parameter . '}', $entryId, $pageConfiguration['id']);
        $entryConfiguration['id'] = $parsedEntryId;
        $entryConfiguration['variables'][$this->variable] = $entry;
        $entryConfiguration['variables']['meta'] = array_merge(
            $entryConfiguration['variables']['meta'] ?? [],
            $this->createMetaVariable($entryConfiguration)
        );

        unset($entryConfiguration['config']['collection']);

        return $entryConfiguration;
    }

    protected function createMetaVariable(array $entryConfiguration): array
    {
        $meta = [];

        if ($title = $this->getTitleMeta($entryConfiguration)) {
            $meta['title'] = $title;
        }

        if ($description = $this->getDescriptionMeta($entryConfiguration)) {
            $meta['description'] = $description;
        }

        return $meta;
    }

    protected function getTitleMeta(array $entryConfiguration): ?string
    {
        $title = $entryConfiguration['variables'][$this->variable]['meta']['title']
            ?? $entryConfiguration['variables'][$this->variable]['title']
            ?? null;

        return $title;
    }

    protected function getDescriptionMeta(array $entryConfiguration): ?string
    {
        $description = $entryConfiguration['variables'][$this->variable]['meta']['description']
            ?? $entryConfiguration['variables'][$this->variable]['description']
            ?? null;

        return $description;
    }
}

<?php

namespace Stitcher\Page\Adapter;

use Stitcher\Page\Adapter;
use Stitcher\Exception\InvalidConfiguration;
use Stitcher\Configureable;
use Stitcher\Variable\VariableParser;

class FilterAdapter implements Adapter, Configureable
{
    /** @var array */
    private $filters;

    /** @var \Stitcher\Variable\VariableParser */
    private $variableParser;

    public function __construct(
        array $adapterConfiguration,
        VariableParser $variableParser
    ) {
        if (! $this->isValidConfiguration($adapterConfiguration)) {
            throw InvalidConfiguration::invalidAdapterConfiguration('filter', '`field`: `filter`');
        }

        $this->filters = $adapterConfiguration;
        $this->variableParser = $variableParser;
    }

    public static function make(
        array $adapterConfiguration,
        VariableParser $variableParser
    ): FilterAdapter {
        return new self($adapterConfiguration, $variableParser);
    }

    public function transform(array $pageConfiguration): array
    {
        foreach ($this->filters as $variableName => $filterConfiguration) {
            $variable = $pageConfiguration['variables'][$variableName] ?? null;

            $entries = $this->variableParser->parse($variable)['entries'] ?? [];

            $filteredEntries = $this->filterEntries($filterConfiguration, $entries);

            $pageConfiguration['variables'][$variableName] = $filteredEntries;
        }

        unset($pageConfiguration['config']['filter']);

        return $pageConfiguration;
    }

    public function isValidConfiguration($subject): bool
    {
        return is_array($subject);
    }

    private function filterEntries($filterConfiguration, $entries): array
    {
        foreach ($filterConfiguration as $filterField => $filterValue) {
            foreach ($entries as $entryId => $entry) {
                $value = $entry[$filterField] ?? null;

                if ($value !== $filterValue) {
                    unset($entries[$entryId]);
                }
            }
        }

        return $entries;
    }
}

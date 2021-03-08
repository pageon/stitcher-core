<?php

namespace Stitcher\Page\Adapter;

use Stitcher\Exception\InvalidCollectionAdapter;
use Stitcher\File;
use Stitcher\Page\Adapter;
use Stitcher\Configureable;
use Stitcher\Variable\VariableParser;
use Symfony\Component\Yaml\Yaml;

class CollectionAdapter implements Adapter, Configureable
{
    /** @var null */
    private static $filterId;

    /** @var array */
    private $entries = [];

    /** @var mixed */
    private $parameter;

    /** @var mixed */
    private $variable;

    /** @var \Stitcher\Variable\VariableParser */
    private $variableParser;

    public function __construct(
        array $adapterConfiguration,
        VariableParser $variableParser
    ) {
        if (! $this->isValidConfiguration($adapterConfiguration)) {
            throw InvalidCollectionAdapter::create();
        }

        $this->variable = $adapterConfiguration['variable'];
        $this->parameter = $adapterConfiguration['parameter'];
        $this->variableParser = $variableParser;
    }

    public static function make(
        array $adapterConfiguration,
        VariableParser $variableParser
    ): CollectionAdapter {
        return new self($adapterConfiguration, $variableParser);
    }

    public static function setFilterId(?string $filterId)
    {
        self::$filterId = $filterId;
    }

    public function transform(array $pageConfiguration): array
    {
        $this->entries = $this->getEntries($pageConfiguration);

        $collectionPageConfiguration = [];

        while ($entry = current($this->entries)) {
            $entryId = key($this->entries);

            if (self::$filterId !== null && self::$filterId != $entryId) {
                next($this->entries);

                continue;
            }

            foreach ($entry as $field => $value) {
                $entry[$field] = $this->variableParser->parse($value);
            }

            $entryConfiguration = $this->createEntryConfiguration(
                $pageConfiguration,
                $entryId,
                $entry
            );

            $collectionPageConfiguration[$entryConfiguration['id']] = $entryConfiguration;

            next($this->entries);
        }

        return $collectionPageConfiguration;
    }

    public function isValidConfiguration($subject): bool
    {
        return \is_array($subject) && isset($subject['variable']) && isset($subject['parameter']);
    }

    protected function getEntries($pageConfiguration): ?array
    {
        $variable = $pageConfiguration['variables'][$this->variable] ?? null;

        return Yaml::parse(File::read($variable));
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

        $entryConfiguration['variables']['_browse'] =
            $entryConfiguration['variables']['_browse']
            ?? $this->createBrowseData();

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
        $title =
            $entryConfiguration['variables'][$this->variable]['meta']['title']
            ?? $entryConfiguration['variables'][$this->variable]['title']
            ?? null;

        return $title;
    }

    protected function getDescriptionMeta(array $entryConfiguration): ?string
    {
        $description =
            $entryConfiguration['variables'][$this->variable]['meta']['description']
            ?? $entryConfiguration['variables'][$this->variable]['description']
            ?? null;

        return $description;
    }

    protected function createBrowseData(): array
    {
        $browse = [];

        $prev = prev($this->entries);

        if (! $prev) {
            reset($this->entries);
        } else {
            $browse['prev'] = $prev;

            next($this->entries);
        }

        $next = next($this->entries);

        if ($next) {
            $browse['next'] = $next;
        }

        prev($this->entries);

        return $browse;
    }
}

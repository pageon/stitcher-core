<?php

namespace Stitcher\Page\Adapter;

use Stitcher\Exception\InvalidOrderAdapter;
use Stitcher\Page\Adapter;
use Stitcher\Configureable;
use Stitcher\Variable\VariableParser;

class OrderAdapter implements Adapter, Configureable
{
    private const REVERSE = [
        'desc',
        'DESC',
        '-',
    ];

    /** @var \Stitcher\Variable\VariableParser */
    private $variableParser;

    /** @var string */
    private $variable;

    /** @var string */
    private $field;

    /** @var string */
    private $direction;

    public function __construct(
        array $adapterConfiguration,
        VariableParser $variableParser
    ) {
        if (! $this->isValidConfiguration($adapterConfiguration)) {
            throw InvalidOrderAdapter::create();
        }

        $this->variable = $adapterConfiguration['variable'];
        $this->field = $adapterConfiguration['field'];
        $this->direction = $adapterConfiguration['direction'] ?? 'asc';

        $this->variableParser = $variableParser;
    }

    public static function make(
        array $adapterConfiguration,
        VariableParser $variableParser
    ): OrderAdapter {
        return new self($adapterConfiguration, $variableParser);
    }

    public function transform(array $pageConfiguration): array
    {
        $entries = $this->getEntries($pageConfiguration);

        $orderedEntries = $this->orderEntries($entries);

        $pageConfiguration['variables'][$this->variable] = $orderedEntries;

        unset($pageConfiguration['config']['order']);

        return [$pageConfiguration];
    }

    public function isValidConfiguration($subject): bool
    {
        return \is_array($subject)
            && isset($subject['variable'])
            && isset($subject['field']);
    }

    private function getEntries($pageConfiguration): ?array
    {
        $variable = $pageConfiguration['variables'][$this->variable] ?? null;

        return $this->variableParser->parse($variable) ?? $variable;
    }

    private function orderEntries(array $entries): array
    {
        uasort($entries, function ($a, $b) {
            return strcmp($a[$this->field] ?? '', $b[$this->field] ?? '');
        });

        if (!in_array($this->direction, self::REVERSE)) {
            $entries = array_reverse($entries, true);
        }

        return $entries;
    }
}

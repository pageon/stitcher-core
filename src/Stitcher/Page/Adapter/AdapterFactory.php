<?php

namespace Stitcher\Page\Adapter;

use Stitcher\Adapter;
use Stitcher\DynamicFactory;
use Stitcher\Variable\VariableParser;

class AdapterFactory extends DynamicFactory
{
    private $variableParser;

    public function __construct(VariableParser $variableParser)
    {
        $this->setCollectionRule();
        $this->setFilterRule();
        $this->setPaginationRule();

        $this->variableParser = $variableParser;
    }

    public static function make(VariableParser $variableParser): AdapterFactory
    {
        return new self($variableParser);
    }

    public function create($adapterType, $adapterConfiguration): ?Adapter
    {
        foreach ($this->getRules() as $rule) {
            $adapter = $rule($adapterType, $adapterConfiguration);

            if ($adapter) {
                return $adapter;
            }
        }

        return null;
    }

    private function setCollectionRule(): void
    {
        $this->setRule(CollectionAdapter::class, function (string $adapterType, array $adapterConfiguration) {
            if ($adapterType === 'collection') {
                return CollectionAdapter::make($adapterConfiguration, $this->variableParser);
            }

            return null;
        });
    }

    private function setFilterRule(): void
    {
        $this->setRule(FilterAdapter::class, function (string $adapterType, array $adapterConfiguration) {
            if ($adapterType === 'filter') {
                return FilterAdapter::make($adapterConfiguration, $this->variableParser);
            }

            return null;
        });
    }

    private function setPaginationRule(): void
    {
        $this->setRule(PaginationAdapter::class, function (string $adapterType, array $adapterConfiguration) {
            if ($adapterType === 'pagination') {
                return PaginationAdapter::make($adapterConfiguration, $this->variableParser);
            }

            return null;
        });
    }
}

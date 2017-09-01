<?php

namespace Brendt\Stitcher\Factory;

use Brendt\Stitcher\Adapter\Adapter;
use Brendt\Stitcher\Exception\UnknownAdapterException;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AdapterFactory
{
    const COLLECTION_ADAPTER = 'collection';
    const PAGINATION_ADAPTER = 'pagination';
    const ORDER_ADAPTER = 'order';
    const FILTER_ADAPTER = 'filter';
    const LIMIT_ADAPTER = 'limit';

    private $container;
    private $adapters = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->addAdapter(self::COLLECTION_ADAPTER, function () {
            return $this->container->get('adapter.collection');
        });

        $this->addAdapter(self::PAGINATION_ADAPTER, function () {
            return $this->container->get('adapter.pagination');
        });

        $this->addAdapter(self::ORDER_ADAPTER, function () {
            return $this->container->get('adapter.order');
        });

        $this->addAdapter(self::FILTER_ADAPTER, function () {
            return $this->container->get('adapter.filter');
        });

        $this->addAdapter(self::LIMIT_ADAPTER, function () {
            return $this->container->get('adapter.limit');
        });
    }

    public function addAdapter(string $adapterName, callable $filter)
    {
        $this->adapters[$adapterName] = $filter;
    }

    public function getByType($type) : Adapter
    {
        if (!isset($this->adapters[$type])) {
            throw new UnknownAdapterException();
        }

        return $this->adapters[$type]($this->container);
    }
}

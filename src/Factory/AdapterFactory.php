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

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * AdapterFactory constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    /**
     * @param $type
     *
     * @return mixed
     *
     * @throws UnknownAdapterException
     */
    public function getByType($type) : Adapter {
        switch ($type) {
            case self::COLLECTION_ADAPTER:
                return $this->container->get('adapter.collection');
            case self::PAGINATION_ADAPTER:
                return $this->container->get('adapter.pagination');
            case self::ORDER_ADAPTER:
                return $this->container->get('adapter.order');
            case self::FILTER_ADAPTER:
                return $this->container->get('adapter.filter');
            default:
                throw new UnknownAdapterException();
        }
    }

}

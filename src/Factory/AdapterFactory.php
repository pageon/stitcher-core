<?php

namespace Brendt\Stitcher\Factory;

use Brendt\Stitcher\Adapter\Adapter;
use Brendt\Stitcher\Adapter\CollectionAdapter;
use Brendt\Stitcher\Adapter\OrderAdapter;
use Brendt\Stitcher\Adapter\PaginationAdapter;
use Brendt\Stitcher\Exception\UnknownAdapterException;

class AdapterFactory
{

    const COLLECTION_ADAPTER = 'collection';

    const PAGINATION_ADAPTER = 'pagination';

    const ORDER_ADAPTER = 'order';

    private $adapters;

    /**
     * @param $type
     *
     * @return Adapter
     *
     * @throws UnknownAdapterException
     */
    public function getByType($type) {
        if (isset($this->adapters[$type])) {
            return $this->adapters[$type];
        }

        switch ($type) {
            case self::COLLECTION_ADAPTER:
                $adapter = new CollectionAdapter();

                break;
            case self::PAGINATION_ADAPTER:
                $adapter = new PaginationAdapter();

                break;
            case self::ORDER_ADAPTER:
                $adapter = new OrderAdapter();

                break;
            default:
                throw new UnknownAdapterException();
        }

        if ($adapter) {
            $this->adapters[$type] = $adapter;
        }

        return $adapter;
    }

}

<?php

namespace brendt\stitcher\factory;

use brendt\stitcher\adapter\Adapter;
use brendt\stitcher\adapter\CollectionAdapter;
use brendt\stitcher\adapter\PaginationAdapter;
use brendt\stitcher\exception\UnknownAdapterException;

class AdapterFactory {

    const COLLECTION_ADAPTER = 'collection';
    const PAGINATION_ADAPTER = 'pagination';

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
            default:
                throw new UnknownAdapterException();
        }

        if ($adapter) {
            $this->adapters[$type] = $adapter;
        }

        return $adapter;
    }

}

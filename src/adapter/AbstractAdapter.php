<?php

namespace brendt\stitcher\adapter;

use brendt\stitcher\Config;
use brendt\stitcher\factory\ProviderFactory;

/**
 * The AbstractAdapter class provides a base for adapters who need to parse template variables.
 */
abstract class AbstractAdapter implements Adapter {

    /**
     * @var ProviderFactory
     */
    protected $providerFactory;

    /**
     * Construct the adapter and set the provider factory variable.
     *
     * @see \brendt\stitcher\factory\ProviderFactory
     */
    public function __construct() {
        $this->providerFactory = Config::getDependency('factory.provider');
    }

    /**
     * This function will get the provider based on the value provided.
     * This value is parsed by the provider, or returned if no suitable provider was found.
     *
     * @param $value
     *
     * @return mixed
     *
     * @see \brendt\stitcher\factory\ProviderFactory
     */
    protected function getData($value) {
        $provider = $this->providerFactory->getProvider($value);

        if (!$provider) {
            return $value;
        }

        return $provider->parse($value);
    }

}

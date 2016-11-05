<?php

namespace brendt\stitcher\adapter;

use brendt\stitcher\Config;
use brendt\stitcher\factory\ProviderFactory;

abstract class AbstractAdapter implements Adapter {

    /** @var ProviderFactory */
    protected $providerFactory;

    public function __construct() {
        $this->providerFactory = Config::getDependency('factory.provider');
    }

    protected function getData($src) {
        $provider = $this->providerFactory->getProvider($src);

        if (!$provider) {
            return $src;
        }

        return $provider->parse($src);
    }

}

<?php

namespace brendt\stitcher\provider;

use brendt\stitcher\Config;
use brendt\stitcher\factory\ProviderFactory;

abstract class AbstractArrayProvider extends AbstractProvider {

    /**
     * @var ProviderFactory
     */
    protected $providerFactory;

    /**
     * AbstractProvider constructor.
     */
    public function __construct() {
        parent::__construct();

        $this->providerFactory = Config::getDependency('factory.provider');
    }

    /**
     * @param array $data
     *
     * @return mixed
     */
    protected function parseArrayData(array $data) {
        $result = [];

        foreach ($data as $id => $entry) {
            $result[$id] = $this->parseEntryData($id, $entry);
        }

        return $result;
    }

    protected function parseEntryData($id, $entry) {
        foreach ($entry as $field => $value) {
            if (is_string($value) && preg_match('/.*\.(md|jpg|png|json|yml)$/', $value) > 0) {
                $provider = $this->providerFactory->getProvider($value);

                if (!$provider) {
                    continue;
                }

                $entry[$field] = $provider->parse(trim($value, '/'));
            } elseif (is_array($value) && array_key_exists('src', $value)) {
                $src = $value['src'];
                $provider = $this->providerFactory->getProvider($src);

                if (!$provider) {
                    continue;
                }

                $entry[$field] = $provider->parse($value);
            }

            if (!isset($entry['id'])) {
                $entry['id'] = $id;
            }
        }

        return $entry;
    }

}

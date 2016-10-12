<?php

namespace brendt\stitcher\provider;

use brendt\stitcher\factory\ProviderFactory;

abstract class AbstractArrayProvider extends AbstractProvider {

    /**
     * @var ProviderFactory
     */
    protected $providerFactory;

    /**
     * AbstractProvider constructor.
     *
     * @param $root
     */
    public function __construct($root) {
        parent::__construct($root);

        $this->providerFactory = new ProviderFactory($root);
    }

    /**
     * @param array $data
     * @param null  $id
     *
     * @return mixed
     */
    protected function parseArrayData(array $data, $id = null) {
        $result = [];

        if (isset($data['entries'])) {
            foreach ($data['entries'] as $id => $entry) {
                $result[$id] = $this->parseArrayData($entry, $id);
            }

            return $result;
        }

        foreach ($data as $field => $value) {
            if (is_string($value) && preg_match('/^\/.*\.(md|jpg|png|json|yml)$/', $value) > 0) {
                $provider = $this->providerFactory->getProvider($value);

                if (!$provider) {
                    continue;
                }

                $result[$field] = $provider->parse(trim($value, '/'));
            } else {
                $result[$field] = $value;
            }
        }

        if (!isset($result['id'])) {
            $result['id'] = $id;
        }

        return $result;
    }

}

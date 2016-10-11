<?php

namespace brendt\stitcher\provider;

use brendt\stitcher\factory\ProviderFactory;

abstract class AbstractProvider implements Provider {

    /**
     * @var
     */
    protected $root;

    protected $providerFactory;

    /**
     * FileDataProvider constructor.
     *
     * @param $root
     */
    public function __construct($root) {
        $this->root = $root;
        $this->providerFactory = new ProviderFactory($root);
    }

    protected function parseArrayData($data, $path, $extension, $parseSingle = false){
        if (!$parseSingle) {
            foreach ($data as $id => $entry) {
                $data[$id] = $this->parseArrayData($entry, $id, $extension, true);
            }

            return $data;
        }

        foreach ($data as $field => $value) {
            if (is_array($value) && isset($value['type']) && isset($value['path'])) {
                $provider = $this->providerFactory->getByType($value['type']);

                if (!$provider) {
                    continue;
                }

                $data[$field] = $provider->parse($value['path']);
            }
        }

        if (!isset($data['id'])) {
            $data['id'] = str_replace($extension, '', $path);
        }

        return $data;
    }

}

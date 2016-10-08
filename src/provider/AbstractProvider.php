<?php

namespace brendt\stitcher\provider;

abstract class AbstractProvider implements Provider {

    /**
     * @var
     */
    protected $root;

    /**
     * FileDataProvider constructor.
     *
     * @param $root
     */
    public function __construct($root) {
        $this->root = $root;
    }

    protected function parseArrayData($data, $path, $extension, $parseSingle = false){
        if ($parseSingle) {
            if (!isset($data['id'])) {
                $data['id'] = str_replace($extension, '', $path);
            }
        } else {
            foreach ($data as $id => $entry) {
                if (isset($entry['id'])) {
                    continue;
                }

                $data[$id]['id'] = $id;
            }
        }

        return $data;
    }

}

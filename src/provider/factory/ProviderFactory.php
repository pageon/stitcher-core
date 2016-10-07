<?php

namespace brendt\stitcher\provider\factory;

use brendt\stitcher\provider\FolderProvider;
use brendt\stitcher\provider\JsonProvider;
use brendt\stitcher\provider\MarkdownProvider;
use brendt\stitcher\provider\Provider;
use brendt\stitcher\provider\YamlProvider;

class ProviderFactory {

    const JSON_PROVIDER = 'json';
    const MARKDOWN_PROVIDER = 'md';
    const FOLDER_PROVIDER = '/';
    const YAML_PROVIDER = 'yml';

    private $providers =  [];

    private $root;

    /**
     * ProviderFactory constructor.
     *
     * @param $root
     */
    public function __construct($root) {
        $this->root = $root;
    }

    /**
     * @param $file
     *
     * @return Provider|null
     */
    public function getProvider($file) {
        if (strpos($file, '/') === strlen($file) - 1) {
            return $this->getByType(self::FOLDER_PROVIDER);
        } else if (strpos($file, '.json') !== false) {
            return $this->getByType(self::JSON_PROVIDER);
        } else if (strpos($file, '.md') !== false) {
            return $this->getByType(self::MARKDOWN_PROVIDER);
        } else if (strpos($file, '.yml') !== false) {
            return $this->getByType(self::YAML_PROVIDER);
        }

        return null;
    }

    /**
     * @param string $type
     *
     * @return Provider|null
     */
    public function getByType($type) {
        if (isset($this->providers[$type])) {
            return $this->providers[$type];
        }

        $provider = null;

        switch ($type) {
            case self::FOLDER_PROVIDER:
                $provider = new FolderProvider($this->root);
                break;
            case self::MARKDOWN_PROVIDER:
                $provider = new MarkdownProvider($this->root);
                break;
            case self::JSON_PROVIDER:
                $provider = new JsonProvider($this->root);
                break;
            case self::YAML_PROVIDER:
                $provider = new YamlProvider($this->root);
                break;
        }

        if ($provider) {
            $this->providers[$type] = $provider;
        }

        return $provider;
    }

}

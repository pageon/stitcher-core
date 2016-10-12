<?php

namespace brendt\stitcher\factory;

use brendt\stitcher\provider\FolderProvider;
use brendt\stitcher\provider\JsonProvider;
use brendt\stitcher\provider\MarkdownProvider;
use brendt\stitcher\provider\Provider;
use brendt\stitcher\provider\YamlProvider;
use brendt\stitcher\provider\ImageProvider;

class ProviderFactory {

    const JSON_PROVIDER = 'json';
    const MARKDOWN_PROVIDER = 'md';
    const FOLDER_PROVIDER = '/';
    const YAML_PROVIDER = 'yml';
    const IMAGE_PROVIDER = 'img';

    private $providers =  [];

    private $root;

    private $publicDir;

    /**
     * ProviderFactory constructor.
     *
     * @param $root
     * @param $publicDir
     */
    public function __construct($root, $publicDir) {
        $this->root = $root;
        $this->publicDir = $publicDir;
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
        } else if (strpos($file, '.jpg') !== false) {
            return $this->getByType(self::IMAGE_PROVIDER);
        } else if (strpos($file, '.png') !== false) {
            return $this->getByType(self::IMAGE_PROVIDER);
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
            case self::IMAGE_PROVIDER:
                $provider = new ImageProvider($this->root);
                break;
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

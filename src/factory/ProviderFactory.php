<?php

namespace brendt\stitcher\factory;

use brendt\stitcher\Config;
use brendt\stitcher\provider\FileProvider;
use brendt\stitcher\provider\FolderProvider;
use brendt\stitcher\provider\ImageProvider;
use brendt\stitcher\provider\JsonProvider;
use brendt\stitcher\provider\MarkdownProvider;
use brendt\stitcher\provider\Provider;
use brendt\stitcher\provider\SassProvider;
use brendt\stitcher\provider\YamlProvider;

class ProviderFactory {

    const JSON_PROVIDER = 'json';

    const MARKDOWN_PROVIDER = 'md';

    const FOLDER_PROVIDER = '/';

    const YAML_PROVIDER = 'yml';

    const IMAGE_PROVIDER = 'img';

    const CSS_PROVIDER = 'css';

    const JS_PROVIDER = 'js';

    const SASS_PROVIDER = 'sass';

    const SCSS_PROVIDER = 'scss';

    private $providers = [];

    private $root;

    private $publicDir;

    /**
     * ProviderFactory constructor.
     */
    public function __construct() {
        $this->root = Config::get('directories.src');
        $this->publicDir = Config::get('directories.public');
    }

    /**
     * @param $fileName
     *
     * @return Provider|null
     */
    public function getProvider($fileName) {
        $provider = null;

        if (strpos($fileName, '/') === strlen($fileName) - 1) {
            $provider = $this->getByType(self::FOLDER_PROVIDER);
        } else if (strpos($fileName, '.json') !== false) {
            $provider = $this->getByType(self::JSON_PROVIDER);
        } else if (strpos($fileName, '.md') !== false) {
            $provider = $this->getByType(self::MARKDOWN_PROVIDER);
        } else if (strpos($fileName, '.yml') !== false) {
            $provider = $this->getByType(self::YAML_PROVIDER);
        } else if (strpos($fileName, '.jpg') !== false) {
            $provider = $this->getByType(self::IMAGE_PROVIDER);
        } else if (strpos($fileName, '.png') !== false) {
            $provider = $this->getByType(self::IMAGE_PROVIDER);
        } else if (strpos($fileName, '.css') !== false) {
            $provider = $this->getByType(self::CSS_PROVIDER);
        } else if (strpos($fileName, '.js') !== false) {
            $provider = $this->getByType(self::JS_PROVIDER);
        } else if (strpos($fileName, '.scss') !== false || strpos($fileName, '.sass') !== false) {
            $provider = $this->getByType(self::SASS_PROVIDER);
        }

        return $provider;
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
                $provider = new ImageProvider();
                break;
            case self::FOLDER_PROVIDER:
                $provider = new FolderProvider();
                break;
            case self::MARKDOWN_PROVIDER:
                $provider = new MarkdownProvider();
                break;
            case self::JSON_PROVIDER:
                $provider = new JsonProvider();
                break;
            case self::YAML_PROVIDER:
                $provider = new YamlProvider();
                break;
            case self::JS_PROVIDER:
                $provider = new FileProvider();
                break;
            case self::CSS_PROVIDER:
                $provider = new FileProvider();
                break;
            case self::SCSS_PROVIDER:
            case self::SASS_PROVIDER:
                $provider = new SassProvider();
                break;
        }

        if ($provider) {
            $this->providers[$type] = $provider;
        }

        return $provider;
    }

}

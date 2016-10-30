<?php

namespace brendt\stitcher\factory;

use brendt\stitcher\Config;
use brendt\stitcher\provider\FileProvider;
use brendt\stitcher\provider\FolderProvider;
use brendt\stitcher\provider\JsonProvider;
use brendt\stitcher\provider\MarkdownProvider;
use brendt\stitcher\provider\Provider;
use brendt\stitcher\provider\YamlProvider;
use brendt\stitcher\provider\ImageProvider;
use brendt\stitcher\provider\SassProvider;

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
        } else if (strpos($file, '.css') !== false) {
            return $this->getByType(self::CSS_PROVIDER);
        } else if (strpos($file, '.js') !== false) {
            return $this->getByType(self::JS_PROVIDER);
        } else if (strpos($file, '.scss') !== false || strpos($file, '.sass') !== false) {
            return $this->getByType(self::SASS_PROVIDER);
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

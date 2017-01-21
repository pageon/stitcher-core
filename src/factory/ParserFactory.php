<?php

namespace brendt\stitcher\factory;

use brendt\stitcher\Config;
use brendt\stitcher\parser\DefaultParser;
use brendt\stitcher\parser\FileParser;
use brendt\stitcher\parser\FolderParser;
use brendt\stitcher\parser\ImageParser;
use brendt\stitcher\parser\JsonParser;
use brendt\stitcher\parser\MarkdownParser;
use brendt\stitcher\parser\Parser;
use brendt\stitcher\parser\SassParser;
use brendt\stitcher\parser\YamlParser;

class ParserFactory {

    const JSON_PARSER = 'json';

    const MARKDOWN_PARSER = 'md';

    const FOLDER_PARSER = '/';

    const YAML_PARSER = 'yml';

    const IMAGE_PARSER = 'img';

    const CSS_PARSER = 'css';

    const JS_PARSER = 'js';

    const SASS_PARSER = 'sass';

    const SCSS_PARSER = 'scss';

    const DEFAULT_PARSER = 'default';

    private $parsers = [];

    private $root;

    private $publicDir;

    /**
     * ParserFactory constructor.
     */
    public function __construct() {
        $this->root = Config::get('directories.src');
        $this->publicDir = Config::get('directories.public');
    }

    /**
     * @param $fileName
     *
     * @return Parser|null
     */
    public function getParser($fileName) {
        if (!is_string($fileName)) {
            return null;
        }

        if (strpos($fileName, '/') === strlen($fileName) - 1) {
            $parser = $this->getByType(self::FOLDER_PARSER);
        } else if (strpos($fileName, '.json') !== false) {
            $parser = $this->getByType(self::JSON_PARSER);
        } else if (strpos($fileName, '.md') !== false) {
            $parser = $this->getByType(self::MARKDOWN_PARSER);
        } else if (strpos($fileName, '.yml') !== false) {
            $parser = $this->getByType(self::YAML_PARSER);
        } else if (strpos($fileName, '.jpg') !== false) {
            $parser = $this->getByType(self::IMAGE_PARSER);
        } else if (strpos($fileName, '.png') !== false) {
            $parser = $this->getByType(self::IMAGE_PARSER);
        } else if (strpos($fileName, '.css') !== false) {
            $parser = $this->getByType(self::CSS_PARSER);
        } else if (strpos($fileName, '.js') !== false) {
            $parser = $this->getByType(self::JS_PARSER);
        } else if (strpos($fileName, '.scss') !== false || strpos($fileName, '.sass') !== false) {
            $parser = $this->getByType(self::SASS_PARSER);
        } else {
            $parser = $this->getByType(self::DEFAULT_PARSER);
        }

        return $parser;
    }

    /**
     * @param string $type
     *
     * @return Parser|null
     */
    public function getByType($type) {
        if (isset($this->parsers[$type])) {
            return $this->parsers[$type];
        }

        $parser = null;

        switch ($type) {
            case self::IMAGE_PARSER:
                $parser = new ImageParser();
                break;
            case self::FOLDER_PARSER:
                $parser = new FolderParser();
                break;
            case self::MARKDOWN_PARSER:
                $parser = new MarkdownParser();
                break;
            case self::JSON_PARSER:
                $parser = new JsonParser();
                break;
            case self::YAML_PARSER:
                $parser = new YamlParser();
                break;
            case self::JS_PARSER:
                $parser = new FileParser();
                break;
            case self::CSS_PARSER:
                $parser = new FileParser();
                break;
            case self::SCSS_PARSER:
            case self::SASS_PARSER:
                $parser = new SassParser();
                break;
            case self::DEFAULT_PARSER:
            default:
                $parser = new DefaultParser();
                break;
        }

        if ($parser) {
            $this->parsers[$type] = $parser;
        }

        return $parser;
    }

}

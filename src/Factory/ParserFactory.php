<?php

namespace Brendt\Stitcher\Factory;

use Brendt\Stitcher\Config;
use Brendt\Stitcher\Parser\DefaultParser;
use Brendt\Stitcher\Parser\FileParser;
use Brendt\Stitcher\Parser\FolderParser;
use Brendt\Stitcher\Parser\ImageParser;
use Brendt\Stitcher\Parser\JsonParser;
use Brendt\Stitcher\Parser\MarkdownParser;
use Brendt\Stitcher\Parser\Parser;
use Brendt\Stitcher\Parser\SassParser;
use Brendt\Stitcher\Parser\YamlParser;

class ParserFactory
{

    const EXTENSION_JSON = 'json';

    const EXTENSION_MD = 'md';

    const EXTENSION_FOLDER = '/';

    const EXTENSION_YML = 'yml';

    const EXTENSION_YAML = 'yaml';

    const EXTENSION_IMG = 'img';

    const EXTENSION_CSS = 'css';

    const EXTENSION_JS = 'js';

    const EXTENSION_SASS = 'sass';

    const EXTENSION_SCSS = 'scss';

    const PARSER_DEFAULT = 'default';

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
            return $this->getByType(self::EXTENSION_FOLDER);
        }

        if (strpos($fileName, '.json') !== false) {
            return $this->getByType(self::EXTENSION_JSON);
        }

        if (strpos($fileName, '.md') !== false) {
            return $this->getByType(self::EXTENSION_MD);
        }

        if (strpos($fileName, '.yml') !== false) {
            return $this->getByType(self::EXTENSION_YML);
        }

        if (strpos($fileName, '.jpg') !== false) {
            return $this->getByType(self::EXTENSION_IMG);
        }

        if (strpos($fileName, '.png') !== false) {
            return $this->getByType(self::EXTENSION_IMG);
        }

        if (strpos($fileName, '.css') !== false) {
            return $this->getByType(self::EXTENSION_CSS);
        }

        if (strpos($fileName, '.js') !== false) {
            return $this->getByType(self::EXTENSION_JS);
        }

        if (strpos($fileName, '.scss') !== false || strpos($fileName, '.sass') !== false) {
            return $this->getByType(self::EXTENSION_SASS);
        }

        return $this->getByType(self::PARSER_DEFAULT);
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

        switch ($type) {
            case self::EXTENSION_IMG:
                $parser = new ImageParser();
                break;
            case self::EXTENSION_FOLDER:
                $parser = new FolderParser();
                break;
            case self::EXTENSION_MD:
                $parser = new MarkdownParser();
                break;
            case self::EXTENSION_JSON:
                $parser = new JsonParser();
                break;
            case self::EXTENSION_YML:
            case self::EXTENSION_YAML:
                $parser = new YamlParser();
                break;
            case self::EXTENSION_JS:
                $parser = new FileParser();
                break;
            case self::EXTENSION_CSS:
                $parser = new FileParser();
                break;
            case self::EXTENSION_SCSS:
            case self::EXTENSION_SASS:
                $parser = new SassParser();
                break;
            case self::PARSER_DEFAULT:
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

<?php

namespace Brendt\Stitcher\Factory;

use Brendt\Stitcher\Parser\Parser;
use Symfony\Component\DependencyInjection\ContainerInterface;

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

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * ParserFactory constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    /**
     * @param $fileName
     *
     * @return Parser|null
     */
    public function getByFileName($fileName) : ?Parser {
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
     * @return mixed
     */
    public function getByType($type) : Parser {
        switch ($type) {
            case self::EXTENSION_IMG:
                return $this->container->get('parser.image');
            case self::EXTENSION_FOLDER:
                return $this->container->get('parser.folder');
            case self::EXTENSION_MD:
                return $this->container->get('parser.markdown');
            case self::EXTENSION_JSON:
                return $this->container->get('parser.json');
            case self::EXTENSION_YML:
            case self::EXTENSION_YAML:
                return $this->container->get('parser.yaml');
            case self::EXTENSION_JS:
            case self::EXTENSION_CSS:
                return $this->container->get('parser.file');
            case self::EXTENSION_SCSS:
            case self::EXTENSION_SASS:
                return $this->container->get('parser.sass');
            case self::PARSER_DEFAULT:
            default:
                return $this->container->get('parser.default');
        }
    }

}

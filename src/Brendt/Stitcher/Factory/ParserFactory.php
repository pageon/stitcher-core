<?php

namespace Brendt\Stitcher\Factory;

use Brendt\Stitcher\Exception\ParserException;
use Brendt\Stitcher\Parser\Parser;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ParserFactory
{
    const EXTENSION_JSON = 'json';
    const EXTENSION_MD = 'markdown';
    const EXTENSION_FOLDER = 'folder';
    const EXTENSION_YML = 'yaml';
    const EXTENSION_YAML = 'yaml';
    const EXTENSION_IMG = 'image';
    const EXTENSION_JS = 'file';
    const EXTENSION_CSS = 'file';
    const EXTENSION_SASS = 'sass';
    const EXTENSION_SCSS = 'sass';
    const PARSER_DEFAULT = 'default';

    private static $typeFilters = [];

    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        self::addTypeFilter(self::EXTENSION_FOLDER, function ($fileName) {
            return substr($fileName, -1) === '/';
        });

        self::addTypeFilter(self::EXTENSION_JSON, function ($fileName) {
            return strpos($fileName, '.json') === strlen($fileName) - 5;
        });

        self::addTypeFilter(self::EXTENSION_MD, function ($fileName) {
            return strpos($fileName, '.md') !== false;
        });

        self::addTypeFilter(self::EXTENSION_YML, function ($fileName) {
            return strpos($fileName, '.yaml') !== false || strpos($fileName, '.yml') !== false;
        });

        self::addTypeFilter(self::EXTENSION_IMG, function ($fileName) {
            return strpos($fileName, '.jpg') !== false || strpos($fileName, '.png') !== false;
        });

        self::addTypeFilter(self::EXTENSION_CSS, function ($fileName) {
            return strpos($fileName, '.css') !== false;
        });

        self::addTypeFilter(self::EXTENSION_JS, function ($fileName) {
            return strpos($fileName, '.js') === strlen($fileName) - 3;
        });

        self::addTypeFilter(self::EXTENSION_SASS, function ($fileName) {
            return strpos($fileName, '.scss') !== false || strpos($fileName, '.sass') !== false;
        });
    }

    public static function addTypeFilter(string $type, callable $filter)
    {
        self::$typeFilters[$type][] = $filter;
    }

    public function getByFileName($fileName)
    {
        if (!is_string($fileName)) {
            return null;
        }

        $type = self::PARSER_DEFAULT;

        /**
         * @var string     $filterType
         * @var callable[] $filters
         */
        foreach (self::$typeFilters as $filterType => $filters) {
            foreach ($filters as $filterCheck) {
                if ($filterCheck($fileName)) {
                    $type = $filterType;
                    break;
                }
            }
        }

        return $this->getByType($type);
    }

    public function getByType($type) : Parser
    {
        $key = "parser.{$type}";

        if (!$this->container->has($key)) {
            throw new ParserException("Parser with the key {$key} not found as a service.");
        }

        return $this->container->get($key);
    }
}

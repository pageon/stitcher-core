<?php

namespace Brendt\Stitcher\Parser;

use Brendt\Stitcher\Exception\ParserException;
use Brendt\Stitcher\Factory\ParserFactory;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use Brendt\Stitcher\Config;

/**
 * The YamlParser take a path to one or more YAML files, and parses the content into an array.
 *
 * @see \Brendt\Stitcher\Parser\AbstractArrayParser::parseArrayData()
 */
class YamlParser extends AbstractArrayParser
{
    /**
     * @var string
     */
    private $srcDir;

    /**
     * JsonParser constructor.
     *
     * @param ParserFactory $parserFactory
     * @param string        $srcDir
     */
    public function __construct(ParserFactory $parserFactory, string $srcDir) {
        parent::__construct($parserFactory);

        $this->srcDir = $srcDir;
    }

    /**
     * @param string $path
     *
     * @return mixed
     * @throws ParserException
     */
    public function parse($path = '*.yml') {
        if (!strpos($path, '.yml')) {
            $path .= '.yml';
        }

        /** @var SplFileInfo[] $files */
        $files = Finder::create()->files()->in($this->srcDir)->path($path);
        $yamlData = [];

        foreach ($files as $file) {
            try {
                $parsed = Yaml::parse($file->getContents());

                if (isset($parsed['entries'])) {
                    $yamlData += $parsed['entries'];
                } else {
                    $id = str_replace(".{$file->getExtension()}", '', $file->getFilename());
                    $yamlData[$id] = $parsed;
                }
            } catch (ParseException $e) {
                throw new ParserException("{$file->getRelativePathname()}: {$e->getMessage()}");
            }
        }

        $parsedEntries = $this->parseArrayData($yamlData);

        return $parsedEntries;
    }

}

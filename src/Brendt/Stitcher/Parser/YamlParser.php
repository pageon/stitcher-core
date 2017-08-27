<?php

namespace Brendt\Stitcher\Parser;

use Brendt\Stitcher\Exception\ParserException;
use Brendt\Stitcher\Factory\ParserFactory;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * The YamlParser take a path to one or more YAML files, and parses the content into an array.
 *
 * @see \Brendt\Stitcher\Parser\AbstractArrayParser::parseArrayData()
 */
class YamlParser extends AbstractArrayParser
{
    private $srcDir;

    public function __construct(ParserFactory $parserFactory, string $srcDir) {
        parent::__construct($parserFactory);

        $this->srcDir = $srcDir;
    }

    public function parse($path = '*.yml') {
        if (!strpos($path, '.yml')) {
            $path .= '.yml';
        }

        /** @var SplFileInfo[] $files */
        $files = Finder::create()->files()->in($this->srcDir)->path($path);
        $data = [];

        foreach ($files as $file) {
            try {
                $parsed = Yaml::parse($file->getContents());

                if (!isset($parsed['entries'])) {
                    $id = str_replace(".{$file->getExtension()}", '', $file->getFilename());
                    $parsed = ['entries' => [$id => $parsed]];
                }

                $data += $parsed['entries'];
            } catch (ParseException $e) {
                throw new ParserException("{$file->getRelativePathname()}: {$e->getMessage()}");
            }
        }

        $parsedEntries = $this->parseArrayData($data);

        return $parsedEntries;
    }
}

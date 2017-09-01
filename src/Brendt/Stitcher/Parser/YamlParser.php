<?php

namespace Brendt\Stitcher\Parser;

use Brendt\Stitcher\Exception\ParserException;
use Brendt\Stitcher\Factory\ParserFactory;
use Brendt\Stitcher\Lib\Browser;
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
    private $browser;

    public function __construct(Browser $browser, ParserFactory $parserFactory)
    {
        parent::__construct($parserFactory);

        $this->browser = $browser;
    }

    public function parse($path)
    {
        if (!strpos($path, '.yml') && !strpos($path, '.yaml')) {
            $path .= '.yml';
        }

        /** @var SplFileInfo[] $files */
        $files = $this->browser->src()->path($path)->files();
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

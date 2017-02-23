<?php

namespace Brendt\Stitcher\Adapter;

use Brendt\Stitcher\Config;
use Brendt\Stitcher\factory\ParserFactory;
use Brendt\Stitcher\Site\Page;

/**
 * The AbstractAdapter class provides a base for adapters who need to parse template variables.
 */
abstract class AbstractAdapter implements Adapter
{

    /**
     * @var ParserFactory
     */
    protected $parserFactory;

    /**
     * Construct the adapter and set the parser factory variable.
     *
     * @see \Brendt\Stitcher\Factory\ParserFactory
     */
    public function __construct() {
        $this->parserFactory = Config::getDependency('factory.parser');
    }

    /**
     * @param Page|Page[] $pages
     * @param mixed  $filter
     *
     * @return Page[]
     */
    public function transform($pages, $filter = null) : array {
        if (!is_array($pages)) {
            $pages = [$pages];
        }

        /** @var Page[] $result */
        $result = [];

        foreach ($pages as $page) {
            $result += $this->transformPage($page, $filter);
        }

        return $result;
    }

    /**
     * This function will get the parser based on the value provided.
     * This value is parsed by the parser, or returned if no suitable parser was found.
     *
     * @param $value
     *
     * @return mixed
     *
     * @see \Brendt\Stitcher\Factory\ParserFactory
     */
    protected function getData($value) {
        $parser = $this->parserFactory->getParser($value);

        if (!$parser) {
            return $value;
        }

        return $parser->parse($value);
    }

}

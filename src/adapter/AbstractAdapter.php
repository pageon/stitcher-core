<?php

namespace brendt\stitcher\adapter;

use brendt\stitcher\Config;
use brendt\stitcher\factory\ParserFactory;

/**
 * The AbstractAdapter class provides a base for adapters who need to parse template variables.
 */
abstract class AbstractAdapter implements Adapter {

    /**
     * @var ParserFactory
     */
    protected $parserFactory;

    /**
     * Construct the adapter and set the parser factory variable.
     *
     * @see \brendt\stitcher\factory\ParserFactory
     */
    public function __construct() {
        $this->parserFactory = Config::getDependency('factory.parser');
    }

    /**
     * This function will get the parser based on the value provided.
     * This value is parsed by the parser, or returned if no suitable parser was found.
     *
     * @param $value
     *
     * @return mixed
     *
     * @see \brendt\stitcher\factory\ParserFactory
     */
    protected function getData($value) {
        $parser = $this->parserFactory->getParser($value);

        if (!$parser) {
            return $value;
        }

        return $parser->parse($value);
    }

}

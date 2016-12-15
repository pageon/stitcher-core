<?php

namespace brendt\stitcher\parser;

use brendt\stitcher\Config;
use brendt\stitcher\factory\ParserFactory;

abstract class AbstractArrayParser extends AbstractParser {

    /**
     * @var ParserFactory
     */
    protected $parserFactory;

    /**
     * AbstractParser constructor.
     */
    public function __construct() {
        parent::__construct();

        $this->parserFactory = Config::getDependency('factory.parser');
    }

    /**
     * @param array $data
     *
     * @return mixed
     */
    protected function parseArrayData(array $data) {
        $result = [];

        foreach ($data as $id => $entry) {
            $result[$id] = $this->parseEntryData($id, $entry);
        }

        return $result;
    }

    protected function parseEntryData($id, $entry) {
        foreach ($entry as $field => $value) {
            if (is_string($value) && preg_match('/.*\.(md|jpg|png|json|yml)$/', $value) > 0) {
                $parser = $this->parserFactory->getParser($value);

                if (!$parser) {
                    continue;
                }

                $entry[$field] = $parser->parse(trim($value, '/'));
            } elseif (is_array($value) && array_key_exists('src', $value)) {
                $src = $value['src'];
                $parser = $this->parserFactory->getParser($src);

                if (!$parser) {
                    continue;
                }

                $entry[$field] = $parser->parse($value);
            }

            if (!isset($entry['id'])) {
                $entry['id'] = $id;
            }
        }

        return $entry;
    }

}

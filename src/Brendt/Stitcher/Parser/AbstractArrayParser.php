<?php

namespace Brendt\Stitcher\Parser;

use Brendt\Stitcher\Factory\ParserFactory;

/**
 * The AbstractArrayParser class provides the abstraction needed to parse arrays of entries provided by
 * eg. the Yaml- or JsonParser.
 *
 * @see \Brendt\Stitcher\Parser\YamlParser
 * @see \Brendt\Stitcher\Parser\JsonParser
 */
abstract class AbstractArrayParser implements Parser
{
    protected $parserFactory;

    public function __construct(ParserFactory $parserFactory)
    {
        $this->parserFactory = $parserFactory;
    }

    /**
     * Parse an array of entries loaded from eg. the Yaml- or JsonParser
     *
     * @param array $data
     *
     * @return mixed
     *
     * @see \Brendt\Stitcher\Parser\YamlParser
     * @see \Brendt\Stitcher\Parser\JsonParser
     */
    protected function parseArrayData(array $data)
    {
        $result = [];

        foreach ($data as $id => $entry) {
            $result[$id] = $this->parseEntryData($id, $entry);
        }

        return $result;
    }

    /**
     * Parse a single entry. An entry has multiple fields with each of them a value. This value can either be a path
     * to another data entry which will be parsed (using the ParserFactory); an array with a key `src` set,
     * which refers to another data entry; or normal data which will be kept the way it was provided.
     *
     * After parsing all fields, an additional check is performed which sets the entry's ID if it wasn't set yet.
     * Finally, an array with parsed fields, representing the entry, is returned.
     *
     * @param $id
     * @param $entry
     *
     * @return array
     *
     * @see \Brendt\Stitcher\Factory\ParserFactory
     */
    protected function parseEntryData($id, $entry)
    {
        $entry = (array) $entry;

        foreach ($entry as $field => $value) {
            if (is_string($value) && preg_match('/.*\.(md|jpg|png|json|yml)$/', $value) > 0) {
                $parser = $this->parserFactory->getByFileName($value);
                $entry[$field] = $parser->parse(trim($value, '/'));
            } elseif (is_array($value) && array_key_exists('src', $value)) {
                $src = $value['src'];
                $parser = $this->parserFactory->getByFileName($src);
                $entry[$field] = $parser->parse($value);
            }

            if (!isset($entry['id'])) {
                $entry['id'] = $id;
            }
        }

        return $entry;
    }
}

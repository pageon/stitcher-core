<?php

namespace Brendt\Stitcher\Exception;

class ConfigurationException extends StitcherException
{

    /**
     * @param       $adapter
     * @param array ...$fields
     *
     * @return ConfigurationException
     */
    public static function requiredAdapterOptions($adapter, ...$fields) : ConfigurationException {
        $entries = count($fields) > 1 ? 'entries' : 'entry';
        $are = count($fields) > 1 ? 'are' : 'is';
        $fields = implode('`, `', $fields);

        return new self("The configuration {$entries} `{$fields}` {$are} required when using the {$adapter} adapter.");
    }

    /**
     * @param $path
     *
     * @return ConfigurationException
     */
    public static function fileNotFound($path) : ConfigurationException {
        return new self("Could not load file: `{$path}`.");
    }

}

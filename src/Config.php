<?php

namespace Brendt\Stitcher;

/**
 * Config helper class
 *
 * @package Brendt\Stitcher
 */
class Config
{
    /**
     * @param        $config
     * @param string $prefix
     *
     * @return array
     */
    public static function flatten(array $config, string $prefix = '') : array {
        $result = [];

        foreach ($config as $key => $value) {
            $new_key = $prefix . (empty($prefix) ? '' : '.') . $key;

            if (is_array($value) && count($value)) {
                $result = array_merge($result, self::flatten($value, $new_key));
            } else {
                $result[$new_key] = $value;
            }
        }

        return $result;
    }
}

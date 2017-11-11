<?php

use Pageon\Config;

if (!function_exists('env')) {
    function env(string $key) {
        return getenv($key);
    }
}

if (!function_exists('config')) {
    function config(string $key) {
        return Config::get($key);
    }
}

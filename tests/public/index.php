<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$root = __DIR__ . '/../../data/public';
$uri = $_SERVER['SCRIPT_NAME'];
$filename = ltrim($uri === '/' ? 'index.html' : "{$uri}.html", '/');

die(@file_get_contents("{$root}/{$filename}"));

<?php

require_once __DIR__ . '/../../vendor/autoload.php';

\Stitcher\File::base(__DIR__ . '/../');

\Stitcher\App::init();

$server = \Stitcher\App::productionServer();

die($server->run());

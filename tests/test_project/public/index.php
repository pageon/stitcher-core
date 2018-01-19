<?php

require_once __DIR__ . '/../../vendor/autoload.php';

\Stitcher\File::base(__DIR__ . '/../');

\Stitcher\App::init();

if (getenv('ENV') === 'development') {
    $server = \Stitcher\App::developmentServer();
} else {
    $server = \Stitcher\App::productionServer();
}

die($server->run());

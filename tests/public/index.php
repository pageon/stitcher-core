<?php

use Stitcher\Application\ProductionServer;

require_once __DIR__ . '/../../vendor/autoload.php';

$rootDirectory = __DIR__ . '/../../data/public';
$server = ProductionServer::make($rootDirectory);

die($server->run());

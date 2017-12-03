<?php

use Stitcher\Application\ProductionServer;

require_once __DIR__ . '/../../vendor/autoload.php';

$server = ProductionServer::make(__DIR__);

die($server->run());

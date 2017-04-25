<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Brendt\Stitcher\Application\DevController;

// This controller will render HTML pages on the fly.
// See config.dev.yml for more information.
$controller = new DevController(__DIR__ . '/../dev');
echo $controller->run();

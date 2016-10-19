<?php

require_once './../vendor/autoload.php';

use brendt\stitcher\controller\DevController;

// Create and run the development controller.
// This controller will render HTML pages on the fly.
// See dev/config.yml for more information.
$controller = new DevController(__DIR__);
$controller->run();

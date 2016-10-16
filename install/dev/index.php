<?php

require_once './../vendor/autoload.php';

use brendt\stitcher\controller\DevController;

$controller = new DevController(__DIR__);
$controller->run();

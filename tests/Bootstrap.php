<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/CommandTestCase.php';

use Symfony\Component\Filesystem\Filesystem;

register_shutdown_function(function () {
    $fs = new Filesystem();

    if ($fs->exists(__DIR__ . '/.cache')) {
        $fs->remove(__DIR__ . '/.cache');
    }

    if ($fs->exists(__DIR__ . '/public')) {
        $fs->remove(__DIR__ . '/public');
    }
});

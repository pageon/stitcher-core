<?php

namespace brendt\stitcher;

require_once './vendor/autoload.php';

use Symfony\Component\Filesystem\Filesystem;

class StitcherInstaller {

    public static function postPackageInstall() {
        $fs = new Filesystem();

        if (!$fs->exists('./src')) {
            $fs->copy(__DIR__ . 'install/src', './');
        }

        if (!$fs->exists('./public')) {
            $fs->mkdir('./public');
        }
    }

}

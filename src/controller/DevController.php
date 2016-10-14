<?php

namespace brendt\stitcher\controller;

use brendt\stitcher\Config;
use brendt\stitcher\Stitcher;

class DevController {

    public function __construct($configPath = './') {
        Config::load($configPath);

        $this->stitcher = new Stitcher();
    }

    public function run() {
        $url = $_SERVER['REQUEST_URI'];

        $site = $this->stitcher->loadSite();

        if (array_key_exists($url, $site)) {
            $blanket = $this->stitcher->stitch($url);
        } else {
            /*
             * Hoe gaan we dit oplossen?
             *  - Handmatige mapping bijhouden na het eenmalig stitchen?
             *  - Loopen over alle routes, en een match berekenen hoeveel de URL lijkt op de route
             *
             */
            $blanket = $this->stitcher->stitch();
        }

        if (array_key_exists($url, $blanket)) {
            echo $blanket[$url];
        }

        return;
    }

}
